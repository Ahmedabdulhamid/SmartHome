<x-filament::page>
    <div class="space-y-4">
        @foreach ($faqs as $faq)
            <x-filament::card
                class="relative p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-300 bg-white">
                {{-- سؤال وجواب --}}
                <details class="group">
                    <summary
                        class="cursor-pointer text-lg font-semibold text-primary-600 flex justify-between items-center select-none">
                        <span>
                            {{ is_array($faq)
                                ? $faq['question'][app()->getLocale()] ?? ''
                                : $faq->getTranslation('question', app()->getLocale()) }}
                        </span>

                        <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform duration-300"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </summary>

                    <div class="mt-3 text-gray-700 leading-relaxed">
                        {!! nl2br(
                            e(is_array($faq) ? $faq['answer'][app()->getLocale()] ?? '' : $faq->getTranslation('answer', app()->getLocale())),
                        ) !!}
                    </div>

                    {{-- **تعديل رئيسي:** نقل الأزرار إلى هنا وإزالة 'absolute' --}}
                    <div class="mt-4 flex gap-2 justify-end">
                        <x-filament::button color="primary" size="sm" tag="a"
                            href="{{ url('/admin/faqs/' . $faq->id . '/edit') }}">
                            {{  __('web.edit') }}
                        </x-filament::button>

                        <x-filament::button color="danger" size="sm" type="button" {{-- **هذا هو المفتاح:** استدعاء دالة Livewire مع الـ ID --}}
                            wire:click="deleteFaq({{ $faq->id }})">
                           {{  __('web.delete') }}
                        </x-filament::button>
                    </div>
                </details>

            </x-filament::card>
        @endforeach

        {{-- في حال عدم وجود أسئلة --}}
        @if ($faqs->isEmpty())
            <div class="text-center text-gray-500 text-lg py-8">
                لا توجد أسئلة شائعة حالياً.
            </div>
        @endif
    </div>
</x-filament::page>
