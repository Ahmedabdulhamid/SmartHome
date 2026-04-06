<?php

namespace App\Repositories\Frontend;

use App\Models\Rfq;

class RfqRepository
{
    public function create(array $attributes): Rfq
    {
        return Rfq::query()->create($attributes);
    }

    public function createItem(Rfq $rfq, array $attributes): void
    {
        $rfq->items()->create($attributes);
    }
}
