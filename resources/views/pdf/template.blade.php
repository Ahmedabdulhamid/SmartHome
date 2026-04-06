@php
    use App\Models\Setting;
    use App\Models\Product;
    use App\Models\ProductVariant;
    //$logoPath = $quotation->template->logo;
    //$absolutePath = storage_path('app/public/' . $logoPath);
    $setting = Setting::first();
    $logoPath = $setting?->site_logo;
    $absolutePath = storage_path('app/public/' . $logoPath);
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>فاتورة #{{ $invoice->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap');

        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #fff;
        }

        .invoice-box {
            max-width: 900px;
            margin: 30px auto;
            padding: 40px;
            border: 1px solid #eee;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            line-height: 24px;
            font-size: 14px;
        }

        .invoice-box h1 {
            font-size: 28px;
            line-height: 28px;
            color: #333;
            margin-bottom: 20px;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: right;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 8px;
            vertical-align: top;
        }

        .invoice-box table tr.heading td {
            background: #f7f7f7;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #333;
            font-weight: bold;
        }

        .logo {
            max-width: 150px;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .invoice-header div {
            text-align: right;
        }

        .invoice-header .company-info {
            text-align: left;
        }

        .invoice-box .notes {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }

        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #fff;
        }

        .status-pending {
            background: #f0ad4e;
        }

        .status-paid {
            background: #5cb85c;
        }

        .status-cancelled {
            background: #d9534f;
        }

        .status-refunded {
            background: #0275d8;
        }

        .status-partially_paid {
            background: #f7b500;
        }
    </style>
</head>

<body>

    <div class="invoice-box">
        <div class="invoice-header">
            <div class="company-info">
                <img src="{{ $absolutePath }}" class="logo" alt="Logo">
                <p>اسم الشركة<br>عنوان الشركة<br>الهاتف: 0123456789</p>
            </div>
            <div class="invoice-info">
                <h1>فاتورة</h1>
                <p>رقم الفاتورة: <strong>{{ $invoice->invoice_number }}</strong></p>
                <p>تاريخ الإصدار: {{ $invoice->issued_at->format('d-m-Y') }}</p>
                <p>حالة الدفع:
                    <span class="status status-{{ $invoice->status }}">
                        {{ ucfirst(str_replace('_', ' ', $invoice->status)) }}
                    </span>
                </p>
            </div>
        </div>

        <table>
            <tr class="heading">
                <td>العميل</td>
                <td>بيانات التواصل</td>
            </tr>
            <tr class="item">
                <td>
                    {{ $sale->user?->name ?? (($sale->order?->f_name ?? '') . ' ' . ($sale->order?->l_name ?? '')) }}<br>
                    {{ $sale->user?->email ?? ($sale->order?->email ?? '') }}
                </td>
                <td>
                    {{ $sale->order?->phone ?? '' }}<br>
                    {{ $sale->order?->address ?? '' }}<br>
                    {{ $sale->order?->city?->getTranslation('name', app()->getLocale()) ?? '' }},
                    {{ $sale->order?->governorate?->getTranslation('name', app()->getLocale()) ?? '' }}
                </td>
            </tr>
        </table>

        <br>

        <table>
            <tr class="heading">
                <td>المنتج</td>
                <td>الوصف</td>
                <td>السعر</td>
                <td>الكمية</td>
                <td>المجموع</td>
            </tr>

            @foreach ($sale->items as $item)
                @php
                    $product = Product::find($item->product_id);
                    $variant = ProductVariant::find($item?->product_variant_id);
                @endphp
                <tr class="item">
                    <td>{{ $product?->getTranslation('name', app()->getLocale()) ?? '-' }}</td>
                    @if (isset($variant))
                        <td>
                            {{ $variant?->getTranslation('name', app()->getLocale()) ?? '-' }}

                        </td>
                    @endif

                    <td>{{ number_format($item->price, 2) }} {{ $sale->currency?->symbol ?? 'EGP' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->total, 2) }} {{ $sale->currency?->symbol ?? 'EGP' }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td colspan="4" style="text-align: left;">المجموع الفرعي:</td>
                <td>{{ number_format($sale->subtotal, 2) }} {{ $sale->currency?->symbol ?? 'EGP' }}</td>
            </tr>
            <tr class="total">
                <td colspan="4" style="text-align: left;">الشحن:</td>
                <td>{{ number_format($sale->shipping_price, 2) }} {{ $sale->currency?->symbol ?? 'EGP' }}</td>
            </tr>
            <tr class="total">
                <td colspan="4" style="text-align: left;">الضريبة:</td>
                <td>{{ number_format($sale->tax, 2) }} {{ $sale->currency?->symbol ?? 'EGP' }}</td>
            </tr>
            <tr class="total">
                <td colspan="4" style="text-align: left;">الإجمالي:</td>
                <td>{{ number_format($sale->total_amount, 2) }} {{ $sale->currency?->symbol ?? 'EGP' }}</td>
            </tr>
        </table>

        <div class="notes">
            <p>ملاحظة: هذه الفاتورة صادرة إلكترونيًا ولا تحتاج توقيع.</p>
        </div>
    </div>

</body>

</html>
