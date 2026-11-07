<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventTicketTier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketTierController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $tiers = $event->tiers()->orderBy('id')->paginate(20);

        return view('admin.ticket-tiers.index', [
            'event' => $event,
            'tiers' => $tiers,
        ]);
    }

    public function create(Event $event): View
    {
        return view('admin.ticket-tiers.create', [
            'event' => $event,
        ]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:event_ticket_tiers,code'],
            'slug' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_downloadable' => ['boolean'],
            'is_show_in_list' => ['boolean'],
            'is_selectable_on_site' => ['boolean'],
            'is_selectable_online' => ['boolean'],
            'seat_qty' => ['integer', 'min:0'],
            'stock_qty' => ['integer', 'min:0'],
            'sold_qty' => ['integer', 'min:0'],
            'is_metasuggest' => ['boolean'],
            'qty' => ['integer', 'min:0'],
        ]);

        $event->tiers()->create($validated);

        return redirect()->route('admin.events.tiers.index', $event)->with('success', 'Tier tiket berhasil dibuat.');
    }

    public function edit(Event $event, EventTicketTier $tier): View
    {
        return view('admin.ticket-tiers.edit', [
            'event' => $event,
            'tier' => $tier,
        ]);
    }

    public function update(Request $request, Event $event, EventTicketTier $tier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:event_ticket_tiers,code,'.$tier->id],
            'slug' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_downloadable' => ['boolean'],
            'is_show_in_list' => ['boolean'],
            'is_selectable_on_site' => ['boolean'],
            'is_selectable_online' => ['boolean'],
            'seat_qty' => ['integer', 'min:0'],
            'stock_qty' => ['integer', 'min:0'],
            'sold_qty' => ['integer', 'min:0'],
            'is_metasuggest' => ['boolean'],
            'qty' => ['integer', 'min:0'],
        ]);

        $tier->update($validated);

        return redirect()->route('admin.events.tiers.index', $event)->with('success', 'Tier tiket berhasil diperbarui.');
    }

    public function destroy(Event $event, EventTicketTier $tier): RedirectResponse
    {
        $tier->delete();

        return redirect()->route('admin.events.tiers.index', $event)->with('success', 'Tier tiket berhasil dihapus.');
    }
}
