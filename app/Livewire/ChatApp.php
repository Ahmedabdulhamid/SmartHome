<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ChatApp extends Component
{
    public $prompt;
    public $history = [];
    public function submit()
    {
        $this->validate([
            'prompt' => ['required']
        ]);

        $questionValue = ['q' => $this->prompt];
        $text = "";
        $webhookUrl = 'http://localhost:5678/webhook-test/chat';

        try {
            $response = Http::timeout(120)->withHeaders([
                'Content-Type' => 'application/json'
            ])->post($webhookUrl, [
                'prompt' => $this->prompt,
                'user_id' => auth()->guard('web')->user()->id // مطابق لـ user_id في n8n
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // أضفنا التحقق من 'answer' لأنه هو الظاهر في الصورة الأخيرة
                if (isset($data['answer'])) {
                    $text = $data['answer'];
                } elseif (isset($data['output'])) {
                    $text = $data['output'];
                } elseif (isset($data['response'])) {
                    $text = $data['response'];
                } else {
                    $text = "الرد وصل بشكل غير متوقع: " . json_encode($data);
                }
            } else {
                $text = "فشل الاتصال بـ n8n. الحالة: " . $response->status();
            }
        } catch (\Exception $e) {
            $text = "حدث خطأ أثناء الإرسال: " . $e->getMessage();
        }

        $questionValue['a'] = $text;
        $this->history[] = $questionValue;
        $this->reset('prompt');
    }
    public function render()
    {
        return view('livewire.chat-app');
    }
}
