<?php

namespace App\Services\Midtrans;

use App\Models\Event;
use Illuminate\Support\Collection;

/**
 * Membangun preview item_details & checklist kesiapan event untuk Midtrans Snap.
 */
class MidtransSyncService
{
    /**
     * Item yang akan dikirim ke Midtrans saat checkout (tier + add-on yang ditampilkan).
     *
     * @return array<int, array{id: string, name: string, price: int, quantity: int}>
     */
    public function itemDetails(Event $event): array
    {
        $items = [];

        foreach ($event->tiers as $tier) {
            $price = (int) round((float) $tier->price);
            if ($price <= 0) {
                continue;
            }
            $items[] = [
                'id' => 'TIER-'.$tier->code,
                'name' => 'Tiket '.$tier->name,
                'price' => $price,
                'quantity' => 1,
            ];
        }

        foreach ($event->addons as $addon) {
            if (! $addon->is_displayed || ! $addon->is_selectable_online) {
                continue;
            }
            $price = (int) round((float) $addon->price);
            if ($price <= 0) {
                continue;
            }
            $items[] = [
                'id' => 'ADDON-'.$addon->code,
                'name' => 'Add-on '.$addon->name,
                'price' => $price,
                'quantity' => 1,
            ];
        }

        return $items;
    }

    /**
     * Checklist kesiapan event untuk Midtrans Snap (untuk ditampilkan di halaman admin).
     *
     * @return array<int, array{label: string, ok: bool, hint: string}>
     */
    public function readiness(Event $event): array
    {
        $event->loadMissing(['tiers', 'addons']);

        $paidTiers = $event->tiers->filter(fn ($tier) => (float) $tier->price > 0);
        $displayedAddons = $event->addons->filter(fn ($addon) => $addon->is_displayed && $addon->is_selectable_online);

        $tiersValid = $event->tiers->every(
            fn ($tier) => is_numeric((string) $tier->price) && (float) $tier->price >= 0
                && is_numeric((string) $tier->fee) && (float) $tier->fee >= 0
        );

        $addonsValid = $event->addons->every(
            fn ($addon) => is_numeric((string) $addon->price) && (float) $addon->price >= 0
        );

        $items = $this->itemDetails($event);

        return [
            [
                'label' => 'Midtrans terkonfigurasi',
                'ok' => ! empty(config('midtrans.server_key')),
                'hint' => 'Server key dikonfigurasi melalui .env (MIDTRANS_SERVER_KEY).',
            ],
            [
                'label' => 'Event dipublikasikan',
                'ok' => (bool) $event->is_published,
                'hint' => 'Hanya event yang dipublikasikan bisa dibeli publik.',
            ],
            [
                'label' => 'Minimal satu tier berbayar',
                'ok' => $paidTiers->isNotEmpty(),
                'hint' => 'Midtrans memerlukan gross_amount >= 1. Buat tier dengan harga di atas 0.',
            ],
            [
                'label' => 'Harga tier valid',
                'ok' => $tiersValid,
                'hint' => 'Harga & fee tier harus numerik dan tidak negatif.',
            ],
            [
                'label' => 'Harga add-on valid',
                'ok' => $addonsValid,
                'hint' => 'Harga add-on harus numerik dan tidak negatif.',
            ],
            [
                'label' => 'Item details akan dikirim',
                'ok' => $items !== [],
                'hint' => count($items).' item (tier + add-on) akan dikirim sebagai item_details.',
            ],
        ];
    }

    /**
     * Apakah semua checklist lolos? (untuk badge status di halaman)
     */
    public function isReady(Event $event): bool
    {
        return Collection::make($this->readiness($event))->every(fn ($item) => $item['ok']);
    }
}
