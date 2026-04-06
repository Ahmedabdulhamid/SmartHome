<?php

namespace App\Support\Livewire;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Redirector;

trait ValidatesWithFormRequest
{
    /**
     * @param  class-string<FormRequest>  $requestClass
     */
    protected function validateWithFormRequest(string $requestClass, array $data): array
    {
        /** @var FormRequest $formRequest */
        $formRequest = app($requestClass);
        $formRequest->setContainer(app());
        $formRequest->setRedirector(app(Redirector::class));
        $formRequest->merge($data);

        abort_unless(app()->call([$formRequest, 'authorize']), 403);

        $rules = app()->call([$formRequest, 'rules']);
        $messages = $formRequest->messages();
        $attributes = $formRequest->attributes();

        $this->withValidator(function ($validator) use ($formRequest): void {
            if (method_exists($formRequest, 'withValidator')) {
                $formRequest->withValidator($validator);
            }

            if (method_exists($formRequest, 'after')) {
                $validator->after(
                    app()->call([$formRequest, 'after'], ['validator' => $validator])
                );
            }
        });

        return $this->validate($rules, $messages, $attributes);
    }
}
