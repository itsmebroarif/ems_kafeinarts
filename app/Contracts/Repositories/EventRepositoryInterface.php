<?php

namespace App\Contracts\Repositories;

use App\Models\Event;

interface EventRepositoryInterface
{
    public function create(array $attributes): Event;

    public function update(Event $event, array $attributes): Event;

    public function find(string $slug): ?Event;

    public function list(array $filters = [], int $page = 1): mixed;
}
