<?php

namespace App\Repositories;

use App\Contracts\Repositories\RegistrantRepositoryInterface;
use App\Models\Event;
use App\Models\Registrant;
use Illuminate\Contracts\Pagination\Paginator;

class RegistrantRepository implements RegistrantRepositoryInterface
{
    public function create(array $attributes): Registrant
    {
        return Registrant::create($attributes);
    }

    public function update(Registrant $registrant, array $attributes): Registrant
    {
        $registrant->update($attributes);

        return $registrant;
    }

    public function findById(int $id): ?Registrant
    {
        return Registrant::find($id);
    }

    public function findByToken(string $participantToken): ?Registrant
    {
        return Registrant::where('participant_token', $participantToken)
            ->where('is_deleted', false)
            ->first();
    }

    public function findBySession(string $sessionKey): ?Registrant
    {
        return Registrant::where('session_key', $sessionKey)
            ->where('is_deleted', false)
            ->first();
    }

    public function list(Event $event, array $filters = [], int $page = 1): Paginator
    {
        $query = $event->registrants()->orderByDesc('id');

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_paid']) && is_bool($filters['is_paid'])) {
            $query->where('is_paid', $filters['is_paid']);
        }

        if (isset($filters['search']) && trim($filters['search']) !== '') {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query->paginate(20, page: $page);
    }

    public function counts(Event $event): array
    {
        $registrants = $event->registrants();

        return [
            'pending' => $registrants->where('status', 'pending')->count(),
            'paid' => $registrants->where('is_paid', true)->count(),
            'confirmed' => $registrants->where('is_confirmed', true)->count(),
            'total' => $registrants->count(),
        ];
    }
}
