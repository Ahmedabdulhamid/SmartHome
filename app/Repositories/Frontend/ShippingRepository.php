<?php

namespace App\Repositories\Frontend;

use App\Models\City;
use App\Models\Governorate;
use App\Models\PaymMethod;
use App\Models\ShippingPrice;
use Illuminate\Database\Eloquent\Collection;

class ShippingRepository
{
    public function getGovernorates(): Collection
    {
        return Governorate::query()->get();
    }

    public function getCitiesByGovernorate(?int $governorateId): Collection
    {
        if (! $governorateId) {
            return City::query()->whereRaw('1 = 0')->get();
        }

        return City::query()->where('governorate_id', $governorateId)->get();
    }

    public function getPaymentMethods(): Collection
    {
        return PaymMethod::query()->get();
    }

    public function findShippingPrice(?int $governorateId, ?int $cityId, ?string $shippingType): ?ShippingPrice
    {
        if (! $governorateId || ! $cityId || ! $shippingType) {
            return null;
        }

        return ShippingPrice::query()
            ->where('governorate_id', $governorateId)
            ->where('city_id', $cityId)
            ->where('shipping_type', $shippingType)
            ->with('currency')
            ->first();
    }
}
