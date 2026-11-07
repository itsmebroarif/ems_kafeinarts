<?php

namespace App\Repositories;

use App\Contracts\Repositories\EventRepositoryInterface;
use App\Models\Event;

class EventRepository implements EventRepositoryInterface
{
    public function create(array $attributes): Event
    {
        return Event::create($attributes);
    }

    public function update(Event $event, array $attributes): Event
    {
        $event->update($attributes);

        return $event->fresh();
    }

    public function find(string $slug): ?Event
    {
        return Event::where('slug', $slug)->first();
    }

    public function list(array $filters = [], int $page = 1): mixed
    {
        $query = Event::query()
            ->with('category')
            ->withCount('registrants');

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_published']) && is_bool($filters['is_published'])) {
            $query->where('is_published', $filters['is_published']);
        }

        if (isset($filters['search']) && trim($filters['search']) !== '') {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        return $query->orderByDesc('id')->paginate(20, page: $page);
    }
}
