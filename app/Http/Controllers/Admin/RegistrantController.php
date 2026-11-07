<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registrant;
use App\Services\EventRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrantController extends Controller
{
    public function __construct(
        private readonly EventRegistrationService $registration,
    ) {}

    public function index(Request $request, Event $event): View
    {
        $registrants = $this->registration->list($event, $request->only(['search', 'status']));

        return view('admin.registrants.index', [
            'event' => $event,
            'registrants' => $registrants,
        ]);
    }

    public function show(Event $event, Registrant $registrant): View
    {
        return view('admin.registrants.show', [
            'event' => $event,
            'registrant' => $registrant,
        ]);
    }

    public function update(Request $request, Event $event, Registrant $registrant): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'guests_quantity' => ['nullable', 'string', 'max:255'],
            'registrant_type' => ['nullable', 'string', 'max:255'],
            'guests_quantity_other' => ['nullable', 'string', 'max:255'],
            'ticket_tier_id' => ['required', 'exists:event_ticket_tiers,id'],
            'voucher_code' => ['nullable', 'string', 'max:255'],
            'addons' => ['array'],
            'addons.*' => ['integer', 'min:0'],
            'custom_fields' => ['array'],
            'custom_fields.*' => ['string', 'max:255'],
            'status' => ['required', 'string', 'in:pending,paid,confirmed,rejected,cancelled,expired'],
            'is_paid' => ['boolean'],
            'is_confirmed' => ['boolean'],
            'is_rejected' => ['boolean'],
            'is_pending' => ['boolean'],
            'notes' => ['nullable', 'string'],
            'shipping_method' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:255'],
        ]);

        $registrant = $this->registration->updateRegistrant($registrant, $validated);

        return redirect()->route('admin.events.registrants.index', $event)->with('success', 'Data pendaftar berhasil diperbarui.');
    }
}
