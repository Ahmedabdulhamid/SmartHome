<?php

namespace App\Services;

use App\Models\Sale;
//use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Log;
//use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class CreateSaleFromOrder
{
    public function handle($order)
{
    DB::transaction(function () use ($order) {

        // Confirmed
        if ($order->status === 'confirmed' && !$order->sale) {
            $sale = Sale::create([
                'order_id' => $order->id,
                'user_id' => $order?->user_id,
                'subtotal' => $order->items->sum('total'),
                'shipping_price' => $order->shipping_price ?? 0,
                'tax' => 0,
                'discount' => 0,
                'total_amount' => $order->total_amount,
                'currency_id' => $order->currency_id,
                'status' => 'paid',
                'sold_at' => now(),
            ]);

            foreach ($order->items as $item) {
                $sale->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                    'currency_id' => $item->currency_id,
                ]);

                $item->product?->decrement('quantity', $item->quantity);
                $item->product_variant?->decrement('quantity', $item->quantity);
            }

            $invoice = $sale->invoice()->create([
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . $sale->id,
                'subtotal' => $sale->subtotal,
                'tax' => $sale->tax,
                'shipping_price' => $sale->shipping_price,
                'discount' => $sale->discount,
                'total_amount' => $sale->total_amount,
                'currency_id' => $sale->currency_id,
                'status' => 'paid',
                'issued_at' => now(),
            ]);

            //$pdf = Pdf::loadView('pdf.template', compact('sale', 'invoice'));
            //$pdfPath = 'invoices/invoice-' . $invoice->id . '.pdf';
            //$pdf->save(storage_path('app/public/' . $pdfPath));
            //$invoice->update(['pdf_path' => $pdfPath]);


        }

        // Cancelled
        if ($order->status === 'cancelled' && $order->sale) {
            $sale = $order->sale;

            foreach ($sale->items as $saleItem) {
                $saleItem->product?->increment('quantity', $saleItem->quantity);
                $saleItem->product_variant?->increment('quantity', $saleItem->quantity);
            }

            $sale->update(['status' => 'cancelled']);
            $sale->invoice?->update(['status' => 'cancelled']);


        }
    });
}

}
