<?php

namespace App\Http\Requests\Livewire;

use Illuminate\Foundation\Http\FormRequest;

class ChatPromptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string'],
        ];
    }
}
