<?php

namespace App\Services\Payment\Exceptions;

use Exception;

class CalculationException extends Exception
{
    public static function tierNotFound(string $tierId): self
    {
        return new self("Ticket tier not found: {$tierId}");
    }

    public static function negativeQuantity(string $addonId, int $qty): self
    {
        return new self("Addon #{$addonId} quantity cannot be negative ({$qty})");
    }

    public static function voucherInvalid(): self
    {
        return new self('Invalid voucher code');
    }

    public static function voucherQuotaExceeded(): self
    {
        return new self('Voucher has reached its usage limit');
    }

    public static function voucherExpired(): self
    {
        return new self('Voucher has expired');
    }

    public static function insufficientStock(string $addonId, int $requested, int $available): self
    {
        return new self("Insufficient stock for addon #{$addonId}. Requested: {$requested}, Available: {$available}");
    }
}
