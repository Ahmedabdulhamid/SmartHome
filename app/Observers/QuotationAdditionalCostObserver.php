<?php

namespace App\Observers;

use App\Models\QuotationAdditionalCost;

class QuotationAdditionalCostObserver
{
    /**
     * Handle the QuotationAdditionalCost "created" event.
     */
    public function created(QuotationAdditionalCost $quotationAdditionalCost): void
    {
        $quotationAdditionalCost->quotation?->recalcTotal();
    }

    /**
     * Handle the QuotationAdditionalCost "updated" event.
     */
    public function updated(QuotationAdditionalCost $quotationAdditionalCost): void
    {
        $quotationAdditionalCost->quotation?->recalcTotal();
    }

    /**
     * Handle the QuotationAdditionalCost "deleted" event.
     */
    public function deleted(QuotationAdditionalCost $quotationAdditionalCost): void
    {
        $quotationAdditionalCost->quotation?->recalcTotal();
    }

    /**
     * Handle the QuotationAdditionalCost "restored" event.
     */
    public function restored(QuotationAdditionalCost $quotationAdditionalCost): void
    {
        //
    }

    /**
     * Handle the QuotationAdditionalCost "force deleted" event.
     */
    public function forceDeleted(QuotationAdditionalCost $quotationAdditionalCost): void
    {
        //
    }
}
