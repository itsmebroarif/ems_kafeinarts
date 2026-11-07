<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RegistrationStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventVoucher;
use App\Models\Registrant;
use App\Services\EventRegistrationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly EventRegistrationService $registration,
    ) {}

    public function index(Request $request): View
    {
        $events = Event::withCount([
            'registrants',
            'registrants as paid_registrants_count' => fn ($query) => $query->where('is_paid', true),
        ])->orderByDesc('id')->paginate(10);

        $event = $request->route('event') instanceof Event ? $request->route('event') : null;

        $metrics = $event
            ? $this->registration->counts($event)
            : ['pending' => 0, 'paid' => 0, 'confirmed' => 0, 'total' => 0, 'vouchers_used' => 0];

        $metrics += [
            'events' => Event::count(),
            'published_events' => Event::where('is_published', true)->count(),
            'revenue' => (float) Registrant::where('is_paid', true)->sum('total'),
            'gross' => (float) Registrant::sum('total'),
        ];

        if (! $event) {
            $metrics['pending'] = Registrant::where('status', RegistrationStatus::PENDING)->count();
            $metrics['paid'] = Registrant::where('is_paid', true)->count();
            $metrics['confirmed'] = Registrant::where('is_confirmed', true)->count();
            $metrics['total'] = Registrant::count();
            $metrics['vouchers_used'] = (int) EventVoucher::sum('uses');
        }

        return view('admin.dashboard.index', [
            'events' => $events,
            'active_event' => $event,
            'metrics' => $metrics,
        ]);
    }
}
