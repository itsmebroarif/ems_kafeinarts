<?php

namespace App\Repositories;

use App\Contracts\Repositories\AddonRepositoryInterface;
use App\Models\Event;
use App\Models\EventAddon;
use App\Services\Payment\Exceptions\CalculationException;

class AddonRepository implements AddonRepositoryInterface
{
    public function listAvailable(Event $event): array
    {
        return $event->addons()
            ->where('is_displayed', true)
            ->where('is_selectable_online', true)
            ->get()
            ->toArray();
    }

    public function decrementStock(EventAddon $addon, int $qty): EventAddon
    {
        $remaining = (int) $addon->qty - (int) $addon->sold_qty;

        if ($qty > $remaining) {
            throw CalculationException::insufficientStock($addon->id, $qty, $remaining);
        }

        $addon->sold_qty = (int) $addon->sold_qty + $qty;
        $addon->save();

        return $addon;
    }
}
