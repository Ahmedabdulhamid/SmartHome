<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    {{-- Essential for dompdf to handle Unicode and proper display --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice No. {{ $invoice->invoice_number }}</title>

    <style>
        /* Base font for general use */
        body {
            font-family: 'Arial', sans-serif; /* Changed to common English font */
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .invoice-box {
            padding: 20px;
        }
        .header h1 {
            color: #007bff;
            font-size: 28px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }
        .details-table thead tr th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left; /* Alignment changed to left for LTR */
        }
        .details-table tbody tr td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left; /* Alignment changed to left for LTR */
            word-wrap: break-word;
        }
        .total-section {
            margin-top: 20px;
            float: right; /* Changed to float right for LTR */
            width: 300px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
        }
        .total-section p {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="invoice-box">

        <table style="width: 100%;">
            <tr>
                {{-- Invoice details on the right side --}}
                <td style="width: 50%; text-align: left;">
                    <h1 style="color: #007bff; border-bottom: none;">INVOICE</h1>
                    <p style="font-size: 16px; font-weight: bold;">Invoice No: {{ $invoice->invoice_number }}</p>
                    <p>Invoice Date: {{ $invoice->invoice_date }}</p>
                    <p>Due Date: {{ $invoice->due_date }}</p>
                </td>
                {{-- Your Company details on the left side --}}
                <td style="width: 50%; text-align: right;">
                    <h3>Company Name: [Your Company Name]</h3>
                    <p>Address: [Company Address]</p>
                    <p>Phone: [Phone Number]</p>
                </td>
            </tr>
        </table>

        <hr style="border: 0; border-top: 1px solid #007bff; margin: 20px 0;">

        {{-- Client Information --}}
        <h3 style="color: #007bff;">Bill To</h3>
        <p style="font-weight: bold;">Client: {{ $invoice->client->name ?? 'N/A' }}</p>
        {{-- Add other client details here if available (Address, Phone, etc.) --}}

        <hr style="margin: 20px 0;">

        {{-- Invoice Items Table --}}
        <h3>Invoice Items</h3>
        <table class="details-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 15%;">Unit Price</th>
                    <th style="width: 25%;">Subtotal (EGP)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($item->price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix">
            <div class="total-section">
                <p>
                    <span>Grand Total:</span>
                    <span>**{{ number_format($invoice->total_amount, 2) }} EGP**</span>
                </p>
            </div>
        </div>

        <div style="clear: both; margin-top: 100px; text-align: center; font-size: 12px; color: #777;">
            <p>Thank you for your business. Please make payment by the due date.</p>
        </div>
    </div>
</body>
</html>
