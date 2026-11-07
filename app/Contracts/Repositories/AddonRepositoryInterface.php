<?php

namespace App\Contracts\Repositories;

use App\Models\Event;
use App\Models\EventAddon;

interface AddonRepositoryInterface
{
    public function listAvailable(Event $event): array;

    public function decrementStock(EventAddon $addon, int $qty): EventAddon;
}
