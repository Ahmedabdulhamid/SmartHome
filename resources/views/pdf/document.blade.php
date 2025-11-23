@php
 use App\Models\Setting;
    //$logoPath = $quotation->template->logo;
    //$absolutePath = storage_path('app/public/' . $logoPath);
    $setting=Setting::first();
    $logoPath=$setting->site_logo;
    $absolutePath = storage_path('app/public/' . $logoPath);
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $translate('quotation_for') }}: {{ $quotation->getTranslation('title', $locale) }}</title>

    <style>
        /* =================================================================
           إعدادات الخطوط العربية (لضمان ظهورها في PDF) وتخطيط RTL
           ================================================================= */
        body {
            /* ⚠️ يجب أن يتطابق 'dejavusans' مع إعداد 'default_font' في دالة generatePdf() */
            font-family: 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 40px;
            font-size: 12px;
            color: #333;
        }

        /* إعادة تعريف أنماط الجدول لـ PDF بدلاً من Tailwind CSS */
        .quotation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .quotation-table th,
        .quotation-table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: right;
        }

        .quotation-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: left;
        }

        /* لتخطيط RTL، النص المحاذي لليمين يصبح محاذياً لليسار */
        .text-left {
            text-align: right;
        }

        /* لتخطيط RTL، النص المحاذي لليسار يصبح محاذياً لليمين */

        /* الأقسام والإجماليات */
        .section {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 20px;
        }

        .header-section {
            display: flex;
            /* Mpdf يدعم Flexbox بشكل أساسي */
            justify-content: space-between;
        }

        .logo-container {
            width: 150px;
            height: 56px;
            /* 3.5rem equivalent */
            text-align: left;
            /* Logo على اليسار في التصميم الأصلي (Order-2) */
        }

        .logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .title-container {
            text-align: right;
        }

        .grid-details {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-top: 10px;
            font-size: 11px;
        }

        .grid-row {
            display: table-row;
        }

        .grid-item {
            display: table-cell;
            width: 25%;
            padding-top: 5px;
        }

        .summary-box {
            width: 350px;
            /* لتحديد عرض عمود الإجماليات */
            float: left;
            /* لتحريك الإجماليات إلى اليسار (نهاية الصفحة في RTL) */
            margin-top: 20px;
        }

        .summary-row {
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
        }

        .grand-total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
            padding-top: 10px;
        }

        .primary-color {
            color: #1e40af;
            /* لون أزرق/رئيسي */
        }
    </style>
</head>

<body>

    <div class="section">
        <div class="header-section">

            <div class="logo-container">

                    {{-- 🟢 تأكد من استخدام asset() أو url() للحصول على المسار الكامل (Absolute URL) --}}
                    <img src="{{ $absolutePath }}" alt="Logo" class="logo">

            </div>

            <div class="title-container">
                <h1 style="font-size: 24px; margin: 0; color: #111;">
                    {{ $quotation->getTranslation('title', $locale) }}
                </h1>
                @if ($quotation->rfq)
                    <p style="font-size: 11px; color: #777; margin-top: 5px;">
                        {{ $translate('from_rfq') }}:
                        <span style="font-weight: bold;">{{ $quotation->rfq->name }}</span>
                    </p>
                @endif
            </div>

        </div>

        <hr style="border: 0; border-top: 1px solid #ddd; margin-top: 15px; margin-bottom: 10px;">

        <div class="grid-details">
            <div class="grid-row">
                <div class="grid-item">
                    <span style="font-weight: bold;">{{ $translate('quotation_no') }}:</span>
                    <span style="font-weight: bold;">{{ $quotation->id }}</span>
                </div>
                <div class="grid-item">
                    <span style="font-weight: bold;">{{ $translate('date') }}:</span>
                    {{ $quotation->date }}
                </div>
                <div class="grid-item">
                    <span style="font-weight: bold;">{{ $translate('delivery_place') }}:</span>
                    {{ $quotation->delivery_place ?? $translate('not_specified') }}
                </div>
                <div class="grid-item">
                    <span style="font-weight: bold;">{{ $translate('delivery_time') }}:</span>
                    {{ $quotation->delivery_time ?? $translate('not_specified') }}
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2 style="font-size: 16px; margin-top: 0;">{{ $translate('quotation_items') }}</h2>
        @if (count($quotation->items) > 0)
            <table class="quotation-table">
                <thead>
                    <tr>

                        <th style="width: 30%;">{{ $translate('product') }}</th>
                        <th style="width: 25%;">{{ $translate('variant') }}</th>
                        <th style="width: 10%;" class="text-center">{{ $translate('qty') }}</th>
                        <th style="width: 10%;" class="text-right">{{ $translate('selling_price') }}</th>
                        <th style="width: 10%;" class="text-right">{{ $translate('tax') }}</th>
                        <th style="width: 15%;" class="text-right">{{ $translate('final_price') }}</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($quotation->items as $index => $item)
                        @php
                            $productName = $item->product->getTranslation('name', $locale);
                            $shortName = mb_substr($productName, 0, 50) . (mb_strlen($productName) > 50 ? '...' : '');
                        @endphp
                        <tr>

                            <td>
                                <p style="font-weight: bold; margin: 0;">{{ $shortName }}</p>
                                <p style="font-size: 10px; color: #777; margin: 2px 0;">{{ $translate('base_price') }}:
                                    {{ $formatCurrency($item->base_price) }}</p>
                            </td>
                            <td>
                                @if ($item->variant)
                                    <p style="margin: 0;">{{ $item->variant->getTranslation('name', $locale) }}</p>
                                    @if ($item->variant->attributeValuesPivot->isNotEmpty())
                                        <div style="font-size: 9px; color: #777; margin-top: 5px;">
                                            @foreach ($item->variant->attributeValuesPivot as $pivot)
                                                <span style="display: block;">
                                                    {{ $pivot->attribute->getTranslation('name', $locale) }}:
                                                    <span
                                                        style="font-weight: bold;">{{ $pivot->attributeValue->value }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <p style="font-size: 10px; color: #777;">({{ $translate('no_variant_applied') }})
                                    </p>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ $formatCurrency($item->selling_price) }}</td>
                            <td class="text-right">{{ $item->tax->rate ?? '0%' }}</td>
                            <td class="text-right" style="font-weight: bold; color: #1e40af;">
                                {{ $formatCurrency($item->final_price) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table
                class="quotation-table w-full border-collapse border border-gray-300 rounded-lg overflow-hidden my-6">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-4 py-3 text-center w-[15%] text-gray-700 dark:text-gray-200 border-r border-gray-300">
                            {{ $translate('tax') }}</th>
                        <th scope="col"
                            class="px-4 py-3 text-right w-[25%] text-gray-700 dark:text-gray-200 border-r border-gray-300">
                            {{ $translate('base_price') }}</th>
                        <th scope="col"
                            class="px-4 py-3 text-center w-[15%] text-gray-700 dark:text-gray-200 border-r border-gray-300">
                            {{ $translate('margin_percentage') }}</th>
                        <th scope="col"
                            class="px-4 py-3 text-right w-[20%] text-gray-700 dark:text-gray-200 border-r border-gray-300">
                            {{ $translate('selling_price') }}</th>
                        <th scope="col"
                            class="px-4 py-3 text-right w-[25%] text-red-600 font-bold dark:text-red-400">
                            {{ $translate('final_price') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">

                        {{-- 1. الضريبة --}}
                        <td class="px-4 py-3 text-center border-r border-gray-200 dark:border-gray-700">
                            @if ($quotation->tax)
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-300">
                                    {{ $quotation->tax->rate }} %</p>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    ({{ $translate('tax_not_applied') }})</p>
                            @endif
                        </td>

                        {{-- 2. سعر الأساس (Base Price) --}}
                        <td class="px-4 py-3 text-right border-r border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                                {{ $formatCurrency($quotation->manual_base_price) }}
                            </p>
                        </td>

                        {{-- 3. نسبة الهامش (Margin Percentage) --}}
                        <td class="px-4 py-3 text-center border-r border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">
                                {{ $quotation->manual_margin_percentage }} %
                            </p>
                        </td>

                        {{-- 4. سعر البيع (Selling Price) --}}
                        <td class="px-4 py-3 text-right border-r border-gray-200 dark:border-gray-700">
                            @php
                                $manualBase = $quotation->manual_base_price ?? 0;
                                $manualMargin = $quotation->manual_margin_percentage ?? 0;
                                // بيع = base + margin
                                $manualSelling = $manualBase + ($manualBase * $manualMargin) / 100;
                            @endphp
                            <p class="text-md font-bold text-blue-600 dark:text-blue-400">
                                {{ $formatCurrency($manualSelling) }}
                            </p>
                        </td>

                        {{-- 5. السعر النهائي (Final Price) --}}
                        <td class="px-4 py-3 text-right">
                            @php
                                // الضريبة (لو فيه tax مربوط بالـ quotation)
                                $taxAmount = 0;
                                if ($quotation->tax) {
                                    $taxAmount = ($manualSelling * $quotation->tax->rate) / 100;
                                }
                                $finalPrice = $manualSelling + $taxAmount;
                            @endphp
                            <p class="text-lg font-extrabold text-red-700 dark:text-red-500">
                                {{ $formatCurrency($finalPrice) }}
                            </p>
                        </td>
                    </tr>
                </tbody>

            </table>
        @endif

    </div>

    <div style="width: 100%; overflow: hidden;">

        @if ($quotation->additionalCosts->isNotEmpty())
            <div class="section summary-box" style="float: right;">
                <h3 style="font-size: 14px; margin-top: 0;">{{ $translate('additional_costs') }}</h3>
                <dl style="margin: 0;">
                    @foreach ($quotation->additionalCosts as $cost)
                        @if ($cost->show_to_customer)
                            <div class="summary-row" style="font-size: 10px; color: #777;">
                                <dt style="font-weight: normal;">{{ __('filament::admin.sub_costs') }} :
                                    {{ $cost?->custom_name ?? __('filament::admin.not_found') }}</dt>
                                <dd>{{ $cost?->custom_value }}</dd>
                            </div>
                        @endif
                        <div class="summary-row" style="font-size: 10px; color: #777;">
                            <dt style="font-weight: normal;">{{ __('filament::admin.main_costs') }} :
                                {{ $cost?->additionalCost?->name ?? __('filament::admin.not_found') }}</dt>

                            <dd>{{ $cost->additionalCost?->getFinalValue($quotation->id) }}</dd>
                        </div>
                    @endforeach
                </dl>
                <hr style="border: 0; border-top: 1px solid #ddd; margin: 10px 0;">
            </div>
        @endif

        <div class="section summary-box" style="float: right; clear: right;">


                <div class="summary-row grand-total">
                    <dt>{{ $translate('grand_total') }}</dt>
                    <dd class="primary-color">
                        {{ $formatCurrency($finalGrandTotal) }}
                    </dd>
                </div>
            </dl>
        </div>

    </div>
</body>

</html>
