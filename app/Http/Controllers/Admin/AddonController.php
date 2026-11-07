<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAddon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddonController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $query = $event->addons()->orderByDesc('id');

        if ($request->has('search') && trim($request->string('search')->value()) !== '') {
            $search = trim($request->string('search')->value());
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $addons = $query->paginate(20);

        return view('admin.addons.index', [
            'event' => $event,
            'addons' => $addons,
        ]);
    }

    public function create(Event $event): View
    {
        return view('admin.addons.create', ['event' => $event]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:event_addons,code'],
            'slug' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_new' => ['boolean'],
            'is_show_in_list' => ['boolean'],
            'is_allow_buying_stack' => ['boolean'],
            'qty' => ['integer', 'min:0'],
            'max_qty' => ['integer', 'min:0'],
            'sold_qty' => ['integer', 'min:0'],
            'is_selectable_on_site' => ['boolean'],
            'is_selectable_online' => ['boolean'],
            'is_metasuggest' => ['boolean'],
            'buy_qty_step' => ['integer', 'min:0'],
            'show_qty' => ['integer', 'min:0'],
            'is_displayed' => ['boolean'],
            'is_displayed_on_backend' => ['boolean'],
            'display_type' => ['string', 'max:255'],
            'is_fit_disable' => ['boolean'],
            'order_by' => ['integer', 'min:0'],
        ]);

        $event->addons()->create($validated);

        return redirect()->route('admin.events.addons.index', $event)->with('success', 'Add-on berhasil dibuat.');
    }

    public function edit(Event $event, EventAddon $addon): View
    {
        return view('admin.addons.edit', [
            'event' => $event,
            'addon' => $addon,
        ]);
    }

    public function update(Request $request, Event $event, EventAddon $addon): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:event_addons,code,'.$addon->id],
            'slug' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_new' => ['boolean'],
            'is_show_in_list' => ['boolean'],
            'is_allow_buying_stack' => ['boolean'],
            'qty' => ['integer', 'min:0'],
            'max_qty' => ['integer', 'min:0'],
            'sold_qty' => ['integer', 'min:0'],
            'is_selectable_on_site' => ['boolean'],
            'is_selectable_online' => ['boolean'],
            'is_metasuggest' => ['boolean'],
            'buy_qty_step' => ['integer', 'min:0'],
            'show_qty' => ['integer', 'min:0'],
            'is_displayed' => ['boolean'],
            'is_displayed_on_backend' => ['boolean'],
            'display_type' => ['string', 'max:255'],
            'is_fit_disable' => ['boolean'],
            'order_by' => ['integer', 'min:0'],
        ]);

        $addon->update($validated);

        return redirect()->route('admin.events.addons.index', $event)->with('success', 'Add-on berhasil diperbarui.');
    }

    public function destroy(Event $event, EventAddon $addon): RedirectResponse
    {
        $addon->delete();

        return redirect()->route('admin.events.addons.index', $event)->with('success', 'Add-on berhasil dihapus.');
    }
}
