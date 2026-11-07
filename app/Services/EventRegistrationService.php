<?php

namespace App\Services;

use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\Registrant;
use App\Services\Payment\CalculationService;
use App\Services\Ticket\TicketGenerationService;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class EventRegistrationService
{
    public function __construct(
        private readonly CalculationService $calculation,
        private readonly TicketGenerationService $ticket,
    ) {}

    public function register(Event $event, array $input, string $sessionKey, string $participantToken): Registrant
    {
        $registrant = DB::transaction(function () use ($event, $input, $sessionKey, $participantToken) {
            $calculation = $this->calculation->calculate($event, $input);

            $registrant = Registrant::create($this->calculation->buildRegistrationPayload(
                $event,
                $input,
                $calculation,
                $sessionKey,
                $participantToken
            ));

            $this->calculation->finalizeRegistration($registrant, $calculation);

            $this->ticket->generate($registrant);

            return $registrant;
        });

        event('event.registration.created', $registrant);

        return $registrant;
    }

    public function confirm(Registrant $registrant): Registrant
    {
        $registrant->is_confirmed = true;
        $registrant->is_pending = false;
        $registrant->status = RegistrationStatus::CONFIRMED;
        $registrant->save();

        event('event.registration.confirmed', $registrant);

        return $registrant;
    }

    public function getRegistrant(string $participantToken): ?Registrant
    {
        return Registrant::where('participant_token', $participantToken)
            ->where('is_deleted', false)
            ->first();
    }

    public function getBySession(string $sessionKey): ?Registrant
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
            'pending' => $registrants->where('status', RegistrationStatus::PENDING)->count(),
            'paid' => $registrants->where('is_paid', true)->count(),
            'confirmed' => $registrants->where('is_confirmed', true)->count(),
            'total' => $registrants->count(),
            'vouchers_used' => $event->vouchers()->sum('uses'),
        ];
    }

    public function updateRegistrant(Registrant $registrant, array $input): Registrant
    {
        $registrant->fill($input);
        $registrant->save();

        return $registrant;
    }
}
