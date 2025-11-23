<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\WhatsappSetting;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsappWebHookController extends Controller
{
 public function handle(Request $request)
    {
       $whatsappSettings = WhatsappSetting::first();


        // التحقق من توافر مفتاح التحقق
        if (empty($whatsappSettings->meta_verify_token)) {
            logger()->error('Meta verification token is missing in settings table.');
            return response('Configuration Error: Missing Verify Token', 500);
        }

        $storedToken = $whatsappSettings->meta_verify_token;

        // 2. معالجة طلب GET (التحقق من صحة Webhook)
        if ($request->isMethod('get')) {
            return $this->verifyWebhook($request, $storedToken);
        }

        // 3. معالجة طلب POST (استقبال الرسائل أو حالات التسليم)
        if ($request->isMethod('post')) {
            return $this->handleIncomingMessage($request, $whatsappSettings);
        }

        return response('Method Not Allowed', 405);
    }
    protected function verifyWebhook(Request $request, $verifyToken)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            logger()->info('Meta Webhook verified successfully.');
            return response($challenge, 200);
        }

        logger()->warning('Meta Webhook verification failed.', [
            'received_token' => $token,
            'expected_token' => $verifyToken,
            'mode' => $mode
        ]);
        return response('Forbidden', 403);
    }
     protected function handleIncomingMessage(Request $request, $settings = [])
    {
        $data = $request->all();

        if (empty($data['entry'][0]['changes'])) {
            return response()->json(['status' => 'No valid changes'], 200);
        }
        logger()->info('Processing incoming WhatsApp message', ['data' => $data]);

        foreach ($data['entry'][0]['changes'] as $change) {
            if ($change['field'] !== 'messages') {
                continue;
            }

            $value = $change['value'];

            // 2. معالجة حالة التسليم (Status Update)
            if (isset($value['statuses'])) {
                foreach ($value['statuses'] as $statusUpdate) {
                    $this->handleMessageStatus($statusUpdate);
                }
            }

            // 3. معالجة الرسائل الواردة (Incoming Message)
            if (isset($value['messages'])) {
                foreach ($value['messages'] as $message) {
                    $this->handleIncomingWhatsAppMessage($message, $value, $settings);
                }
            }
        }

        return response()->json(['status' => 'Success'], 200);
    }
     protected function handleMessageStatus($statusUpdate)
    {
        $messageId = $statusUpdate['id'];
        $status = $statusUpdate['status']; // 'sent', 'delivered', 'read'

        // البحث عن الرسالة الصادرة باستخدام معرف Meta (twilio_message_sid)
        $message = ChatMessage::where('whatsapp_message_sid', $messageId)->first();

        if ($message) {
            $message->status = $status;
            $message->save();

            // 🚀 إطلاق الحدث لتحديث الحالة في الواجهة الأمامية
            //
            // يتطلب هذا الحدث MessageStatusUpdated أن يكون منشأً ومُعداً للبث.
            // يمكنك استخدام NewIncomingWhatsAppMessage إذا كان أبسط.
            //event(new NewIncomingWhatsAppMessage($message));

            logger()->info("Status updated for message ID: {$messageId} to {$status}");
        }
    }
     protected function handleIncomingWhatsAppMessage($message, $value, $settings)
    {
        $senderNumber = $message['from'];
        $wamid = $message['id'];

        $externalNumber = 'whatsapp:' . $senderNumber;

        // 2. البحث عن المحادثة أو إنشائها
        $conversation = Conversation::firstOrCreate(
            ['external_number' => $externalNumber],
            [
                'status' => 'open',
                'unread_count' => 0,
                'last_message_at' => Carbon::now(),
            ]
        );

        // 3. تحليل المحتوى
        $messageBody = '';
        $messageType = $message['type'];
        $attachmentUrl = null;

        if ($messageType === 'text') {
            $messageBody = $message['text']['body'];
        } elseif (in_array($messageType, ['image', 'video', 'document'])) {
            $messageBody = "Received a {$messageType} attachment.";
            // ... منطق جلب الميديا ...
        } else {
            $messageBody = "Received unsupported message type: {$messageType}";
        }

        // 4. حفظ الرسالة في قاعدة البيانات
        $newMessage = ChatMessage::create([ // 💡 تخزين الرسالة في متغير $newMessage
            'conversation_id' => $conversation->id,
            'body' => $messageBody,
            'direction' => 'inbound',
            'admin_id' => null,
            'status' => 'delivered',
            'whatsapp_message_sid' => $wamid,
            'type' => $messageType,
            'attachment_url' => $attachmentUrl,
            'seen_by_user' => false,
        ]);

        // 5. 🚀 إطلاق حدث Pusher (يتم فوراً بعد حفظ الرسالة)
        // هذا السطر يبث الرسالة الجديدة للواجهة الأمامية
        //event(new NewIncomingWhatsAppMessage($newMessage));

        // 6. تحديث حالة المحادثة
        $conversation->increment('unread_count');
        $conversation->last_message_at = Carbon::now();
        $conversation->save();

        logger()->info("New inbound message received from: {$senderNumber}");
    }
}
