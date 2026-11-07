<?php

namespace App\Services\Payment;

use App\Models\Registrant;
use App\Services\Payment\Exceptions\CalculationException;

class PaymentSimulationService
{
    public function simulate(Registrant $registrant): array
    {
        $event = $registrant->event;
        $tier = $registrant->ticketTier;

        $subtotal = (float) $tier->price;
        $addonTotal = 0.0;

        foreach ($registrant->addons as $ra) {
            $addonTotal += round((float) $ra->price * (int) $ra->qty, 2);
        }

        $discount = 0.0;
        $voucher = null;

        if ($registrant->voucher_id) {
            $voucher = $registrant->voucher;
            $discount = $voucher->is_percent
                ? round($subtotal * (float) $voucher->value / 100, 2)
                : round((float) $voucher->value, 2);
        }

        $total = round($subtotal + $addonTotal - $discount, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'addons' => $addonTotal,
            'discount' => $discount,
            'fee' => round((float) $tier->fee, 2),
            'total' => $total,
            'voucher' => $voucher ? $voucher->toArray() : null,
            'currency' => config('app.currency', 'IDR'),
        ];
    }

    public function capture(Registrant $registrant, array $payload): array
    {
        $this->validateSimulation($registrant, $payload);

        $registrant->is_paid = true;
        $registrant->is_pending = false;
        $registrant->status = 'paid';
        $registrant->paid_at = now();
        $registrant->paid_by = $payload['provider'] ?? 'manual';
        $registrant->exchange_id = $payload['exchange_id'] ?? null;
        $registrant->ip_address = $payload['ip_address'] ?? null;
        $registrant->device_info = $payload['device_info'] ?? null;
        $registrant->device_type = $payload['device_type'] ?? 'desktop';
        $registrant->os_version = $payload['os_version'] ?? null;
        $registrant->browser = $payload['browser'] ?? null;
        $registrant->browser_version = $payload['browser_version'] ?? null;
        $registrant->locale = $payload['locale'] ?? 'id';
        $registrant->save();

        event('event.registration.paid', $registrant);

        return [
            'status' => 'success',
            'transaction_id' => $payload['exchange_id'] ?? uniqid('txn_', true),
            'amount' => $registrant->total,
            'currency' => 'IDR',
            'message' => 'Pembayaran berhasil diproses',
        ];
    }

    public function handleWebhook(Registrant $registrant, array $payload): array
    {
        $this->validateSimulation($registrant, $payload);

        $registrant->is_paid = true;
        $registrant->is_pending = false;
        $registrant->status = 'paid';
        $registrant->paid_at = now();
        $registrant->exchange_id = $payload['exchange_id'] ?? null;
        $registrant->ip_address = $payload['ip_address'] ?? null;
        $registrant->device_info = $payload['device_info'] ?? null;
        $registrant->save();

        event('event.registration.paid', $registrant);

        return [
            'status' => 'success',
            'exchange_id' => $registrant->exchange_id,
        ];
    }

    public function validateRegistration(Registrant $registrant): bool
    {
        if ($registrant->is_deleted) {
            throw CalculationException::tierNotFound('0');
        }

        if ($registrant->is_deleted) {
            throw CalculationException::tierNotFound('0');
        }

        return true;
    }

    private function validateSimulation(Registrant $registrant, array $payload): void
    {
        if (! isset($payload['exchange_id']) || $payload['exchange_id'] === '') {
            throw CalculationException::tierNotFound('0');
        }

        if (! isset($payload['amount']) || (float) $payload['amount'] <= 0) {
            throw CalculationException::tierNotFound('0');
        }
    }
}
