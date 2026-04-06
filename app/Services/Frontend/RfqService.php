<?php

namespace App\Services\Frontend;

use App\Models\Admin;
use App\Models\Currency;
use App\Notifications\RfqNontification;
use App\Repositories\Frontend\RfqRepository;
use Filament\Notifications\Notification;

class RfqService
{
    public function __construct(
        private readonly RfqRepository $rfqs,
    ) {}

    public function getCurrencyId(string $currencyCode): ?int
    {
        return Currency::query()->whereCode($currencyCode)->value('id');
    }

    public function submit(array $validated, int $currencyId)
    {
        $rfq = $this->rfqs->create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'description' => $validated['description'] ?? null,
            'currency_id' => $currencyId,
            'expected_price' => $validated['rfq_expected_price'] ?: null,
        ]);

        foreach ($validated['items'] ?? [] as $item) {
            if (empty($item['product_id'])) {
                continue;
            }

            $this->rfqs->createItem($rfq, [
                'product_id' => $item['product_id'],
                'product_variant_id' => $item['product_variant_id'] ?: null,
                'quantity' => $item['quantity'],
                'expected_price' => $item['expected_price'] ?? null,
            ]);
        }

        $admins = Admin::query()->get();

        foreach ($admins as $admin) {
            if (! $admin->hasRole('sales')) {
                continue;
            }

            $recipient = Admin::query()->where('email', 'sales@gmail.com')->first() ?? $admin;
            $recipient->notify(new RfqNontification($rfq));

            Notification::make()
                ->title('You Have a new RFQ from ' . $rfq->name)
                ->success()
                ->sendToDatabase($recipient);
        }

        return $rfq;
    }
}
