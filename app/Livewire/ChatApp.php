<?php

namespace App\Livewire;

use App\Http\Requests\Livewire\ChatPromptRequest;
use App\Services\Frontend\ChatWebhookService;
use App\Support\Livewire\ValidatesWithFormRequest;
use Livewire\Component;

class ChatApp extends Component
{
    use ValidatesWithFormRequest;

    public $prompt;
    public $history = [];

    public function submit(): void
    {
        $validated = $this->validateWithFormRequest(ChatPromptRequest::class, [
            'prompt' => $this->prompt,
        ]);

        $questionValue = ['q' => $validated['prompt']];

        try {
            $questionValue['a'] = app(ChatWebhookService::class)->ask(
                $validated['prompt'],
                (int) auth()->guard('web')->user()->id,
            );
        } catch (\Throwable $exception) {
            report($exception);
            $questionValue['a'] = 'An error occurred while sending: ' . $exception->getMessage();
        }

        $this->history[] = $questionValue;
        $this->reset('prompt');
    }

    public function render()
    {
        return view('livewire.chat-app');
    }
}
