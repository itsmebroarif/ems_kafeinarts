<?php

namespace App\Services\Ticket\Exceptions;

use Exception;

class TicketGenerationException extends Exception
{
    public static function noTicketGenerated(int $registrantId): self
    {
        return new self("No ticket generated for registrant #{$registrantId}");
    }
}
