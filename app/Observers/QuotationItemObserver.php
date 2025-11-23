<?php

namespace App\Observers;

use App\Models\QuotationItem;

class QuotationItemObserver
{
    /**
     * Handle the QuotationItem "created" event.
     */
    public function created(QuotationItem $item): void
    {

        $item->quotation?->recalcTotal();
    }

    /**
     * Handle the QuotationItem "updated" event.
     */
    public function updated(QuotationItem $item): void
    {
        $item->quotation?->recalcTotal();
    }

    /**
     * Handle the QuotationItem "deleted" event.
     */
    public function deleted(QuotationItem $item): void
    {
        $item->quotation?->recalcTotal();
    }

    /**
     * Handle the QuotationItem "restored" event.
     */
    public function restored(QuotationItem $quotationItem): void
    {
        //
    }

    /**
     * Handle the QuotationItem "force deleted" event.
     */
    public function forceDeleted(QuotationItem $quotationItem): void
    {
        //
    }
}
