<?php

namespace App\Repositories;

use App\Contracts\Repositories\VoucherRepositoryInterface;
use App\Models\Event;
use App\Models\EventVoucher;

class VoucherRepository implements VoucherRepositoryInterface
{
    public function findActiveByCode(Event $event, string $code): ?EventVoucher
    {
        return $event->vouchers()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    public function decrementQuota(EventVoucher $voucher): EventVoucher
    {
        $voucher->uses = (int) $voucher->uses + 1;
        $voucher->save();

        return $voucher;
    }
}
