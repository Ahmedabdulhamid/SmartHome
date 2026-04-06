<?php

namespace App\Services\Frontend;

use Illuminate\Support\Facades\Http;

class ChatWebhookService
{
    public function ask(string $prompt, int $userId): string
    {
        $response = Http::timeout(120)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post('http://localhost:5678/webhook-test/chat', [
                'prompt' => $prompt,
                'user_id' => $userId,
            ]);

        if (! $response->successful()) {
            return 'فشل الاتصال بـ n8n. الحالة: ' . $response->status();
        }

        $data = $response->json();

        if (isset($data['answer'])) {
            return (string) $data['answer'];
        }

        if (isset($data['output'])) {
            return (string) $data['output'];
        }

        if (isset($data['response'])) {
            return (string) $data['response'];
        }

        return 'الرد وصل بشكل غير متوقع: ' . json_encode($data);
    }
}
