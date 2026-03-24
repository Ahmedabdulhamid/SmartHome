<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Conversation;
use App\Models\WhatsappSetting;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class WhatsappWebHookController extends Controller
{
    public function handle(Request $request)
    {
        $whatsappSettings = WhatsappSetting::first();



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

        // 1. البحث عن المحادثة أو إنشائها
        $conversation = Conversation::firstOrCreate(
            ['external_number' => $externalNumber],
            [
                'status' => 'open',
                'unread_count' => 0,
                'last_message_at' => Carbon::now(),
            ]
        );

        // 2. تحليل المحتوى
        $messageBody = '';
        // ... داخل الدالة handleIncomingWhatsAppMessage

        $messageType = $message['type'] ?? 'unknown';
        $attachmentUrl = null;

        if ($messageType === 'text') {
            $messageBody = $message['text']['body'] ?? '';
        } else {
            // استخراج الـ ID بطريقة آمنة
            $mediaId = $message[$messageType]['id'] ?? null;

            if ($mediaId) {
                $attachmentUrl = $this->downloadWhatsappMedia($mediaId, $settings);
                $messageBody = "Sent a " . $messageType;
                logger()->info("Media ID is".$mediaId);
            } else {
                // لو لسه بيطلع null، سجل النوع عشان نعرف المشكلة فين
                logger()->warning("Media ID not found for type: {$messageType}", ['message' => $message]);
                $messageBody = "Received " . $messageType . " (Media ID missing)";
            }
        }

        // 3. حفظ الرسالة
        $newMessage = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'external_number' => $externalNumber,
            'body' => $messageBody,
            'direction' => 'inbound',
            'status' => 'delivered',
            'whatsapp_message_sid' => $wamid,
            'type' => $messageType, // الآن يقبل أي نص لأننا حولناه لـ string
            'attachment_url' => $attachmentUrl,
            'seen_by_user' => false,
        ]);

        $conversation->increment('unread_count');
        $conversation->last_message_at = Carbon::now();
        $conversation->save();

        logger()->info("New message saved: {$messageType} from {$senderNumber}");
    }
    protected function downloadWhatsappMedia($mediaId, $settings)
{
    try {
        $accessToken = $settings->meta_access_token;

        // الخطوة 1: الحصول على رابط الملف من Meta
        $response = Http::withToken($accessToken)
            ->withUserAgent('Mozilla/5.0') // إضافة User-Agent لتجنب الحظر
            ->get("https://graph.facebook.com/v21.0/{$mediaId}"); // جرب تحديث الإصدار لـ v21.0

        if (!$response->successful()) {
            logger()->error("Meta Step 1 Fail: " . $response->body());
            return null;
        }

        $mediaUrl = $response->json()['url'] ?? null;
        if (!$mediaUrl) return null;

        // الخطوة 2: تحميل الملف الفعلي باستخدام الرابط المستلم
        // ملاحظة: الرابط المستلم من Meta أحياناً يتطلب التوكن وأحياناً لا، الأفضل تمريره
        $fileResponse = Http::withToken($accessToken)
            ->withUserAgent('Mozilla/5.0')
            ->get($mediaUrl);

        if (!$fileResponse->successful()) {
            logger()->error("Meta Step 2 (Download) Fail: " . $fileResponse->status());
            return null;
        }

        // الخطوة 3: معالجة اسم الملف والامتداد
        $fileContents = $fileResponse->body();
        $mimeType = $fileResponse->header('Content-Type');

        // تحسين استخراج الامتداد
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'audio/ogg'  => 'ogg',
            'audio/mpeg' => 'mp3',
            'audio/amr'  => 'amr',
            'video/mp4'  => 'mp4',
            'application/pdf' => 'pdf'
        ];
        $extension = $extensions[$mimeType] ?? 'bin';

        $fileName = 'wa_' . $mediaId . '_' . time() . '.' . $extension;
        $filePath = 'uploads/whatsapp/' . $fileName;

        // التأكد من وجود المجلد والحفظ
        Storage::disk('public')->put($filePath, $fileContents);

        return asset('storage/' . $filePath);

    } catch (\Exception $e) {
        logger()->error("Exception in download: " . $e->getMessage());
        return null;
    }
}

}
