
<x-filament-panels::page>

    @php

        $quotation = $this->quotation;
        $currencyCode = $quotation->currency->code ?? 'USD';
        $locale = app()->getLocale();



        $formatCurrency = fn($amount) => number_format($amount, 2) . ' ' . $currencyCode;

        // دالة مساعدة لترجمة العناوين
        $translate = fn($key, $lang = null) => __("filament::admin.{$key}", [], $lang ?? $locale);

        // حساب إجمالي التكاليف الإضافية التي ستظهر للعميل
        $customerVisibleCosts = $quotation->additionalCosts->filter(fn($cost) => $cost->show_to_customer)->sum('value');

        // الإجمالي النهائي للعرض
        $finalGrandTotal = $quotation->total ?? $quotation->items->sum('final_price') + $customerVisibleCosts;
    @endphp
    <div class="space-y-6">

        {{-- ====================================== 1. رأس العرض (العنوان + Logo + معلومات) ====================================== --}}
        <x-filament::section>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">


                <div class="mb-6 flex justify-end">

                    <button wire:click="generatePdf" wire:loading.attr="disabled" wire:loading.class="opacity-70"
                        type="button"
                        class="filament-button inline-flex items-center justify-center font-semibold rounded-lg border
                     text-white bg-primary-600 hover:bg-primary-500 py-2 px-4 transition duration-200 shadow-md">

                        <svg wire:loading.remove wire:target="generatePdf" class="h-5 w-5 rtl:ml-2"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M10 2a1 1 0 00-1 1v7.586L7.293 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V3a1 1 0 00-1-1zM3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" />
                        </svg>

                        <svg wire:loading wire:target="generatePdf" class="h-5 w-5 rtl:ml-2 animate-spin"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>

                        {{ __('filament::admin.dwonload_pdf') }}
                    </button>
                </div>

                {{-- ====================================== نهاية الزر ====================================== --}}
                <div class="order-1 md:order-2">

                        {{-- افترضنا أن لديك حقل 'logo_url' في موديل Template --}}
                        <img src="{{ $setting?->site_logo ? Storage::url($setting->site_logo) : '' }}" alt="Logo"
                            class="h-14 object-contain">



                </div>

                {{-- العنوان والمعلومات الأساسية --}}
                <div class="order-2 md:order-1">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                        {{ $quotation->getTranslation('title', $locale) }}
                    </h1>

                    @if ($quotation->rfq)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $translate('from_rfq') }}: <span class="font-medium">{{ $quotation->rfq->name }}</span>
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                          {{ $translate('email') }} :  <span class="font-medium">{{ $quotation->rfq->email }}</span>
                        </p>
                         <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                          {{ $translate('phone') }} :  <span class="font-medium">{{ $quotation->rfq->phone }}</span>
                        </p>
                         <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                          {{ $translate('description') }} :  <span class="font-medium">{{ $quotation->rfq?->description ?? $translate('not_found') }}</span>
                        </p>
                    @endif
                </div>
            </div>

            <hr class="mt-4 mb-2 border-gray-200 dark:border-gray-700">

            {{-- تفاصيل التاريخ والتسليم --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 dark:text-gray-300">
                <div class="col-span-1">
                    <span class="font-semibold">{{ $translate('quotation_no') }}:</span>
                    <span class="font-bold">{{ $quotation->id }}</span>
                </div>
                <div class="col-span-1">
                    <span class="font-semibold">{{ $translate('date') }}:</span>
                    {{ $quotation->date }}
                </div>
                <div class="col-span-1">
                    <span class="font-semibold">{{ $translate('delivery_place') }}:</span>
                    {{ $quotation->delivery_place ?? $translate('not_specified') }}
                </div>
                <div class="col-span-1">
                    <span class="font-semibold">{{ $translate('delivery_time') }}:</span>
                    {{ $quotation->delivery_time ?? $translate('not_specified') }}
                </div>
            </div>
        </x-filament::section>

        ---
        @if (count($quotation->items) > 0)
            <x-filament::section :heading="$translate('quotation_items')" class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-3 py-3 w-[5%]">#</th>
                            <th scope="col" class="px-3 py-3 w-[30%]">{{ $translate('product') }}</th>
                            <th scope="col" class="px-3 py-3 w-[25%]">{{ $translate('variant') }}</th>
                            <th scope="col" class="px-3 py-3 text-center w-[5%]">{{ $translate('qty') }}</th>
                            <th scope="col" class="px-3 py-3 text-right w-[10%]">{{ $translate('selling_price') }}
                            </th>
                            <th scope="col" class="px-3 py-3 text-right w-[10%]">{{ $translate('tax') }}</th>
                            <th scope="col" class="px-3 py-3 text-right w-[15%]">{{ $translate('final_price') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quotation->items as $index => $item)
                            @php
                                $productName = $item->product?->getTranslation('name', $locale) ?? $translate('not_found');
                                $shortName =
                                    mb_substr($productName, 0, 50) . (mb_strlen($productName) > 50 ? '...' : '');
                            @endphp
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-3 py-3 font-medium">{{ $index + 1 }}</td>

                                {{-- اسم المنتج (مختصر) --}}
                                <td class="px-3 py-3">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $shortName }}</p>
                                    <p class="text-xs text-gray-400">{{ $translate('base_price') }}:
                                        {{ $formatCurrency($item->base_price) }}</p>
                                </td>

                                {{-- المتغيرات --}}
                                <td class="px-3 py-3">
                                    @if ($item->variant)
                                        <p class="font-normal">{{ $item->variant?->getTranslation('name', $locale) ?? $translate('not_found') }}
                                        </p>
                                        @if ($item->variant->attributeValuesPivot->isNotEmpty())
                                            <div class="text-xs text-gray-400 mt-1 space-y-0.5">
                                                @foreach ($item->variant->attributeValuesPivot as $pivot)
                                                    <span class="block">
                                                        {{ $pivot->attribute?->getTranslation('name', $locale) ?? $translate('not_found') }}:
                                                        <span
                                                            class="font-medium">{{ $pivot->attributeValue?->value ?? '-' }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-xs text-gray-400">({{ $translate('no_variant_applied') }})</p>
                                    @endif
                                </td>

                                <td class="px-3 py-3 text-center">{{ $item->quantity }}</td>
                                <td class="px-3 py-3 text-right">{{ $formatCurrency($item->selling_price) }}</td>
                                <td class="px-3 py-3 text-right">
                                    @if (!$item->tax)
                                        <span
                                            class="text-xs text-red-500">({{ $translate('tax_not_found') }})</span><br>
                                    @else
                                        <span class="text-xs text-gray-400">
                                            ({{ $item->tax->rate }})
                                            %
                                        </span><br>
                                    @endif

                                </td>
                                <td class="px-3 py-3 text-right font-bold text-primary-600 dark:text-primary-400">
                                    {{ $formatCurrency($item->getFinalPrice()) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


            </x-filament::section>
        @else
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-center w-[5%]">{{ $translate('tax') }}</th>

                        <th scope="col" class="px-3 py-3 w-[30%]">{{ $translate('base_price') }}</th>
                        <th scope="col" class="px-3 py-3 w-[25%]">{{ $translate('margin_percentage') }}</th>
                        <th scope="col" class="px-3 py-3 text-center w-[5%]">{{ $translate('selling_price') }}</th>

                        <th scope="col" class="px-3 py-3 text-center w-[5%]">{{ $translate('final_price') }}</th>


                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">



                        <td class="px-3 py-3">
                            @if ($quotation->tax)
                                <p class="text-xs text-gray-400">{{ $translate('tax') }}: {{ $quotation->tax->rate }}
                                    %</p>
                            @else
                                <p class="text-xs text-gray-400">({{ $translate('tax_not_found') }}) %</p>
                            @endif


                        </td>

                        <td class="px-3 py-3">

                            <p class="text-xs text-gray-400">{{ $translate('base_price') }}:
                                {{ $formatCurrency($quotation->manual_base_price) }}</p>
                        </td>

                        {{-- المتغيرات --}}
                        <td class="px-3 py-3">
                            <p class="text-xs text-gray-400">{{ $quotation->manual_margin_percentage }} %</p>
                        </td>


                        <td class="px-3 py-3 text-right">
                            {{ $formatCurrency(
                                $quotation->manual_base_price + ($quotation->manual_margin_percentage * $quotation->manual_base_price) / 100,
                            ) }}

                        </td>
                        <td class="px-3 py-3 text-right">
                            @php
                                $manualBase = $quotation->manual_base_price ?? 0;
                                $manualMargin = $quotation->manual_margin_percentage ?? 0;

                                // بيع = base + margin
                                $manualSelling = $manualBase + ($manualBase * $manualMargin) / 100;

                                // الضريبة (لو فيه tax مربوط بالـ quotation)
                                $taxAmount = 0;
                                if ($quotation->tax) {
                                    $taxAmount = ($manualSelling * $quotation->tax->rate) / 100;
                                }

                                $finalPrice = $manualSelling + $taxAmount;
                            @endphp

                            {{ $formatCurrency($finalPrice) }}
                        </td>


                    </tr>
                </tbody>
            </table>
        @endif


        ---

        {{-- ====================================== 3. الإجماليات والتكاليف الإضافية والقالب ====================================== --}}
        <div class="max-w-full">

            {{-- عمود القالب (المنتصف) --}}

            {{-- عمود الإجماليات (العرض الكامل لـ additional costs) --}}
            <div class="w-full">


                @if ($quotation->additionalCosts)

                    <x-filament::section :heading="$translate('additional_costs')" class="max-w-full">
                        <dl class="text-sm space-y-1 w-full">
                            @foreach ($quotation->additionalCosts as $cost)
                                @php
                                    $costClass = $cost->show_to_customer ? 'font-medium' : 'text-xs text-gray-400';
                                    $costName = $cost->custom_name;
                                    $costValue =
                                        $formatCurrency($cost->custom_value) ?? __('filament::admin.not_found');
                                    $mainCostName = $cost->additionalCost->name ?? __('filament::admin.not_found');
                                    $mainCostValue = $cost->additionalCost->value ?? __('filament::admin.not_found');
                                @endphp
                                <div class="flex justify-between w-full {{ $costClass }}">
                                    <dt class="font-normal">{{ $translate('main_costs') }}: {{ $mainCostName }}</dt>
                                    <dd class="font-medium">{{ __('filament::admin.value') }} : {{ $mainCostValue }}
                                    </dd>
                                    <dt class="font-normal">{{ $translate('sub_costs') }}: {{ $costName }}</dt>
                                    <dd class="font-medium">{{ __('filament::admin.value') }} : {{ $costValue }}
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                        <hr class="my-2 border-gray-200 dark:border-gray-700 w-full">
                    </x-filament::section>
                @else
                    {{ __('filament::admin.not_found') }}
                @endif

                {{-- جدول الإجماليات النهائية --}}
                <x-filament::section class="max-w-full">
                    <dl class="text-base space-y-2 w-full">

                        <div class="flex justify-between text-xl font-extrabold w-full">
                            <dt>{{ $translate('grand_total') }}</dt>
                            <dd class="text-primary-600 dark:text-primary-400">
                                {{ $formatCurrency($quotation->total) }}
                            </dd>
                        </div>
                    </dl>
                </x-filament::section>

            </div>

        </div>

        ---



    </div>
</x-filament-panels::page>
