<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventVoucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $query = $event->vouchers()->orderByDesc('id');

        if ($request->has('search') && trim($request->string('search')->value()) !== '') {
            $search = trim($request->string('search')->value());
            $query->where('code', 'like', "%{$search}%");
        }

        $vouchers = $query->paginate(20);

        return view('admin.vouchers.index', [
            'event' => $event,
            'vouchers' => $vouchers,
        ]);
    }

    public function create(Event $event): View
    {
        return view('admin.vouchers.create', ['event' => $event]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:event_vouchers,code'],
            'is_percent' => ['boolean'],
            'value' => ['required', 'numeric', 'min:0'],
            'is_single_use' => ['boolean'],
            'is_active' => ['boolean'],
            'total_uses' => ['integer', 'min:0'],
            'uses' => ['integer', 'min:0'],
            'is_auto_apply' => ['boolean'],
            'is_show_in_list' => ['boolean'],
            'is_show_on_front' => ['boolean'],
            'is_show_on_payment' => ['boolean'],
            'expired_at' => ['nullable', 'date'],
            'order_by' => ['integer', 'min:0'],
        ]);

        $event->vouchers()->create($validated);

        return redirect()->route('admin.events.vouchers.index', $event)->with('success', 'Voucher berhasil dibuat.');
    }

    public function edit(Event $event, EventVoucher $voucher): View
    {
        return view('admin.vouchers.edit', [
            'event' => $event,
            'voucher' => $voucher,
        ]);
    }

    public function update(Request $request, Event $event, EventVoucher $voucher): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', 'unique:event_vouchers,code,'.$voucher->id],
            'is_percent' => ['boolean'],
            'value' => ['required', 'numeric', 'min:0'],
            'is_single_use' => ['boolean'],
            'is_active' => ['boolean'],
            'total_uses' => ['integer', 'min:0'],
            'uses' => ['integer', 'min:0'],
            'is_auto_apply' => ['boolean'],
            'is_show_in_list' => ['boolean'],
            'is_show_on_front' => ['boolean'],
            'is_show_on_payment' => ['boolean'],
            'expired_at' => ['nullable', 'date'],
            'order_by' => ['integer', 'min:0'],
        ]);

        $voucher->update($validated);

        return redirect()->route('admin.events.vouchers.index', $event)->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Event $event, EventVoucher $voucher): RedirectResponse
    {
        $voucher->delete();

        return redirect()->route('admin.events.vouchers.index', $event)->with('success', 'Voucher berhasil dihapus.');
    }
}
