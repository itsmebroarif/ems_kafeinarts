<?php

namespace App\Contracts\Repositories;

use App\Models\Event;
use App\Models\Registrant;

interface RegistrantRepositoryInterface
{
    public function create(array $attributes): Registrant;

    public function update(Registrant $registrant, array $attributes): Registrant;

    public function findById(int $id): ?Registrant;

    public function findByToken(string $participantToken): ?Registrant;

    public function findBySession(string $sessionKey): ?Registrant;

    public function list(Event $event, array $filters = [], int $page = 1): mixed;

    public function counts(Event $event): array;
}
