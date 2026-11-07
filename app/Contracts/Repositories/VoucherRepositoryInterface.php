<?php

namespace App\Contracts\Repositories;

use App\Models\Event;
use App\Models\EventVoucher;

interface VoucherRepositoryInterface
{
    public function findActiveByCode(Event $event, string $code): ?EventVoucher;

    public function decrementQuota(EventVoucher $voucher): EventVoucher;
}
