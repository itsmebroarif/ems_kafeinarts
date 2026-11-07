<?php

namespace App\Enums;

class EventListener
{
    public const REGISTER = 'event.registration.created';

    public const CONFIRM = 'event.registration.confirmed';

    public const PAYMENT_CAPTURED = 'event.registration.paid';

    public const TICKET_GENERATED = 'event.ticket.generated';
}
