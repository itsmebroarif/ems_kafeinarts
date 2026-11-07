<?php

namespace App\Services\Payment;

use App\Models\Event;
use App\Models\EventAddon;
use App\Models\Registrant;
use App\Services\Payment\Exceptions\CalculationException;
use Illuminate\Support\Facades\DB;

class CalculationService
{
    public function calculate(Event $event, array $input): array
    {
        $tier = $event->tiers()->find($input['ticket_tier_id']);
        if (! $tier) {
            throw CalculationException::tierNotFound($input['ticket_tier_id']);
        }

        $subtotal = (float) $tier->price;
        $addons = $this->resolveAddons($event, $input, $tier);
        $addonsTotal = 0.0;
        $addonDetails = [];

        foreach ($addons as $addon) {
            $price = round((float) $addon->price, 2);
            $qty = (int) ($input['addons'][$addon->id] ?? 0);
            if ($qty < 0) {
                throw CalculationException::negativeQuantity($addon->id, $qty);
            }
            $addonsTotal += $price * $qty;
            $addonDetails[] = [
                'addon_id' => $addon->id,
                'name' => $addon->name,
                'qty' => $qty,
                'price' => $price,
                'fee' => round((float) $addon->fee, 2),
                'amount' => round($price * $qty, 2),
            ];
        }

        $discountCode = trim((string) ($input['voucher_code'] ?? ''));
        $discount = 0.0;
        $discountDetails = [];
        $voucher = null;

        if ($discountCode !== '') {
            [$voucher, $discount] = $this->resolveVoucher($event, $discountCode, $subtotal);
            $discountDetails = [
                'code' => $voucher ? $voucher->code : $discountCode,
                'amount' => $discount,
                'is_percent' => $voucher ? $voucher->is_percent : null,
            ];
        }

        $total = round($subtotal + $addonsTotal - $discount, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'addons' => $addonsTotal,
            'addon_details' => $addonDetails,
            'discount' => round($discount, 2),
            'discount_details' => $discountDetails,
            'fee' => round($tier->fee, 2),
            'total' => $total,
            'tier' => $tier,
            'voucher' => $voucher,
        ];
    }

    public function buildRegistrationPayload(
        Event $event,
        array $input,
        array $calculation,
        string $sessionKey,
        string $participantToken
    ): array {
        $registrant = new Registrant;
        $registrant->full_name = $input['full_name'];
        $registrant->email = $input['email'];
        $registrant->phone = $input['phone'] ?? null;
        $registrant->nationality = $input['nationality'] ?? null;
        $registrant->city = $input['city'] ?? null;
        $registrant->district = $input['district'] ?? null;
        $registrant->address = $input['address'] ?? null;
        $registrant->postal_code = $input['postal_code'] ?? null;
        $registrant->guests_quantity = $input['guests_quantity'] ?? null;
        $registrant->registrant_type = $input['registrant_type'] ?? null;
        $registrant->guests_quantity_other = $input['guests_quantity_other'] ?? null;
        $registrant->event_id = $event->id;
        $registrant->ticket_tier_id = $input['ticket_tier_id'];
        $registrant->voucher_id = $calculation['voucher'] ? $calculation['voucher']->id : null;
        $registrant->subtotal = (string) round($calculation['subtotal'], 2);
        $registrant->addons_total = (string) round($calculation['addons'], 2);
        $registrant->discount = (string) round($calculation['discount'], 2);
        $registrant->total = (string) round($calculation['total'], 2);
        $registrant->fee = (string) round($calculation['fee'], 2);
        $registrant->status = 'pending';
        $registrant->session_key = $sessionKey;
        $registrant->participant_token = $participantToken;
        $registrant->custom_fields = $input['custom_fields'] ?? null;

        return $registrant->toArray();
    }

    public function finalizeRegistration(Registrant $registrant, array $calculation): void
    {
        $addonDetails = $calculation['addon_details'] ?? [];

        DB::transaction(function () use ($registrant, $calculation, $addonDetails) {
            $registrant->subtotal = (string) round($calculation['subtotal'], 2);
            $registrant->addons_total = (string) round($calculation['addons'], 2);
            $registrant->discount = (string) round($calculation['discount'], 2);
            $registrant->total = (string) round($calculation['total'], 2);
            $registrant->fee = (string) round($calculation['fee'], 2);
            $registrant->status = 'pending';
            $registrant->save();

            $this->reserveInventory($registrant, $addonDetails);
            $this->persistAddonSelections($registrant, $addonDetails);
        });
    }

    private function resolveAddons(Event $event, array $input, $tier): array
    {
        $addons = $event->addons()
            ->with('options')
            ->where('is_displayed', true)
            ->where('is_selectable_online', true)
            ->get();

        return $addons->filter(fn ($addon) => ! $addon->is_allow_buying_stack || (int) ($input['addons'][$addon->id] ?? 0) <= (int) $addon->max_qty)->values()->all();
    }

    private function resolveVoucher(Event $event, string $code, float $subtotal): array
    {
        $voucher = $event->vouchers()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $voucher) {
            throw CalculationException::voucherInvalid();
        }

        if ($voucher->is_percent) {
            $amount = round($subtotal * (float) $voucher->value / 100, 2);
        } else {
            $amount = round((float) $voucher->value, 2);
        }

        if ($voucher->uses >= $voucher->total_uses) {
            throw CalculationException::voucherQuotaExceeded();
        }

        if ($voucher->expired_at && $voucher->expired_at->isPast()) {
            throw CalculationException::voucherExpired();
        }

        $voucher->uses++;
        $voucher->save();

        return [$voucher, $amount];
    }

    /**
     * @param  array<int, array{addon_id: string, qty: int, price: float, fee: float}>  $addonDetails
     */
    private function reserveInventory(Registrant $registrant, array $addonDetails): void
    {
        foreach ($addonDetails as $detail) {
            $qty = (int) ($detail['qty'] ?? 0);
            if ($qty <= 0) {
                continue;
            }

            $addon = EventAddon::find($detail['addon_id'] ?? null);
            if (! $addon) {
                continue;
            }

            $remaining = (int) $addon->qty - (int) $addon->sold_qty;

            if ($qty > $remaining) {
                throw CalculationException::insufficientStock($addon->id, $qty, $remaining);
            }

            $addon->sold_qty += $qty;
            $addon->save();
        }
    }

    /**
     * Menyimpan pilihan add-on peserta ke tabel registrant_addons.
     *
     * @param  array<int, array{addon_id: string, qty: int, price: float, fee: float}>  $addonDetails
     */
    private function persistAddonSelections(Registrant $registrant, array $addonDetails): void
    {
        foreach ($addonDetails as $detail) {
            $qty = (int) ($detail['qty'] ?? 0);
            if ($qty <= 0) {
                continue;
            }

            $registrant->addons()->create([
                'addon_id' => $detail['addon_id'],
                'qty' => $qty,
                'price' => (string) round((float) ($detail['price'] ?? 0), 2),
                'fee' => (string) round((float) ($detail['fee'] ?? 0), 2),
            ]);
        }
    }
}
