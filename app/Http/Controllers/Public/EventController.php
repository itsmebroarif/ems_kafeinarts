<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Services\EventRegistrationService;
use App\Services\Payment\Exceptions\CalculationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        private readonly EventRegistrationService $registration,
    ) {}

    public function index(Request $request): View
    {
        $search = trim($request->string('search')->value());
        $activeCategory = trim($request->string('category')->value());
        $activeType = trim($request->string('type')->value());
        $activeCity = trim($request->string('city')->value());
        $activePrice = trim($request->string('price')->value());
        $activeDate = trim($request->string('date')->value());

        $buildBase = function (
            bool $ignoreCategory = false,
            bool $ignoreType = false,
            bool $ignoreCity = false,
            bool $ignorePrice = false,
            bool $ignoreDate = false
        ) use ($activeCategory, $activeType, $activeCity, $activePrice, $activeDate) {
            $q = Event::where('is_published', true);

            if (! $ignoreCategory && $activeCategory !== '') {
                $q->whereHas('category', fn ($c) => $c->where('slug', $activeCategory));
            }

            if (! $ignoreType && $activeType !== '') {
                $q->where('event_type', $activeType);
            }

            if (! $ignoreCity && $activeCity !== '') {
                $q->where('city', $activeCity);
            }

            if (! $ignorePrice && $activePrice === 'free') {
                $q->where('price', '<=', 0);
            } elseif (! $ignorePrice && $activePrice === 'paid') {
                $q->where('price', '>', 0);
            }

            if (! $ignoreDate && $activeDate === 'today') {
                $q->whereDate('starts_at', today());
            } elseif (! $ignoreDate && $activeDate === 'week') {
                $q->whereBetween('starts_at', [now()->startOfDay(), now()->endOfWeek()->endOfDay()]);
            } elseif (! $ignoreDate && $activeDate === 'month') {
                $q->whereBetween('starts_at', [now()->startOfDay(), now()->endOfMonth()->endOfDay()]);
            } elseif (! $ignoreDate && $activeDate === 'upcoming') {
                $q->where('starts_at', '>=', now());
            }

            return $q;
        };

        // Main query
        $query = $buildBase()->with('category')->withCount('registrants')->orderByDesc('id');

        $query->when($search !== '', function ($q) use ($search) {
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        });

        $events = $query->paginate(9)->withQueryString();

        // Category counts (against current type/city/price/date)
        $categories = EventCategory::query()
            ->active()
            ->whereHas('events', fn ($q) => $q->where('is_published', true))
            ->withCount(['events as published_events_count' => function ($q) use ($activeType, $activeCity, $activePrice, $activeDate) {
                $q->where('is_published', true);
                if ($activeType !== '') {
                    $q->where('event_type', $activeType);
                }
                if ($activeCity !== '') {
                    $q->where('city', $activeCity);
                }
                if ($activePrice === 'free') {
                    $q->where('price', '<=', 0);
                } elseif ($activePrice === 'paid') {
                    $q->where('price', '>', 0);
                }
                if ($activeDate === 'today') {
                    $q->whereDate('starts_at', today());
                } elseif ($activeDate === 'week') {
                    $q->whereBetween('starts_at', [now()->startOfDay(), now()->endOfWeek()->endOfDay()]);
                } elseif ($activeDate === 'month') {
                    $q->whereBetween('starts_at', [now()->startOfDay(), now()->endOfMonth()->endOfDay()]);
                } elseif ($activeDate === 'upcoming') {
                    $q->where('starts_at', '>=', now());
                }
            }])
            ->orderBy('name')
            ->get();

        // Type counts
        $typeCounts = [
            'kompetisi' => (clone $buildBase(false, true))->where('event_type', 'kompetisi')->count(),
            'hiburan' => (clone $buildBase(false, true))->where('event_type', 'hiburan')->count(),
        ];

        // City counts (top 10)
        $cityCounts = (clone $buildBase(false, false, true))
            ->selectRaw('city, count(*) as total')
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('total')
            ->pluck('total', 'city');

        // Price counts
        $priceCounts = [
            'free' => (clone $buildBase(false, false, false, true))->where('price', '<=', 0)->count(),
            'paid' => (clone $buildBase(false, false, false, true))->where('price', '>', 0)->count(),
        ];

        // Date counts
        $dateCounts = [
            'today' => (clone $buildBase(false, false, false, false, true))->whereDate('starts_at', today())->count(),
            'week' => (clone $buildBase(false, false, false, false, true))->whereBetween('starts_at', [now()->startOfDay(), now()->endOfWeek()->endOfDay()])->count(),
            'month' => (clone $buildBase(false, false, false, false, true))->whereBetween('starts_at', [now()->startOfDay(), now()->endOfMonth()->endOfDay()])->count(),
            'upcoming' => (clone $buildBase(false, false, false, false, true))->where('starts_at', '>=', now())->count(),
        ];

        return view('public.events.index', [
            'events' => $events,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'activeType' => $activeType,
            'activeCity' => $activeCity,
            'activePrice' => $activePrice,
            'activeDate' => $activeDate,
            'typeCounts' => $typeCounts,
            'cityCounts' => $cityCounts,
            'priceCounts' => $priceCounts,
            'dateCounts' => $dateCounts,
            'filters' => [
                'search' => $request->input('search'),
                'type' => $activeType,
                'city' => $activeCity,
                'price' => $activePrice,
                'date' => $activeDate,
            ],
        ]);
    }

    public function show($slug): View
    {
        $event = Event::with('category')->where('slug', $slug)->where('is_published', true)->firstOrFail();

        $tiers = $event->tiers()->orderBy('price')->get();

        return view('public.events.show', [
            'event' => $event,
            'tiers' => $tiers,
        ]);
    }

    public function stage(Request $request, string $slug): View
    {
        $event = Event::where('slug', $slug)->where('is_published', true)->firstOrFail();

        $tierId = $request->input('tier_id');
        $tier = $event->tiers()->find($tierId);

        if (! $tier) {
            return redirect()->route('public.events.show', $slug);
        }

        $voucherCode = $request->input('voucher_code', Session::get('voucher_code', ''));

        return view('public.events.stage', [
            'event' => $event,
            'tier' => $tier,
            'voucher_code' => $voucherCode,
            'addons' => $event->addons()->where('is_displayed', true)->get(),
        ]);
    }

    public function confirm(Request $request, string $slug): View
    {
        $event = Event::where('slug', $slug)->where('is_published', true)->firstOrFail();

        $tierId = $request->input('tier_id');
        $tier = $event->tiers()->find($tierId);

        if (! $tier) {
            return redirect()->route('public.events.show', $slug);
        }

        $payload = Session::get('wizard_registration', []);

        if (empty($payload)) {
            return redirect()->route('public.events.stage', $slug);
        }

        return view('public.events.confirm', [
            'event' => $event,
            'tier' => $tier,
            'payload' => $payload,
        ]);
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $event = Event::where('slug', $slug)->where('is_published', true)->firstOrFail();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'nik' => ['required', 'digits:16'],
            'nationality' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'village' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:255'],
            'guests_quantity' => ['nullable', 'string', 'max:255'],
            'registrant_type' => ['nullable', 'string', 'max:255'],
            'guests_quantity_other' => ['nullable', 'string', 'max:255'],
            'ticket_tier_id' => ['required', 'exists:event_ticket_tiers,id'],
            'voucher_code' => ['nullable', 'string', 'max:255'],
            'addons' => ['array'],
            'addons.*' => ['integer', 'min:0', 'max:1'],
            'custom_fields' => ['array'],
            'custom_fields.*' => ['string', 'max:255'],
        ]);

        $payload = $validated;
        $payload['ticket_tier_id'] = $validated['ticket_tier_id'];

        $sessionKey = 'wizard_'.$request->ip().'_'.uniqid();
        Session::put($sessionKey, $payload);

        $participantToken = 'PART-'.strtoupper(uniqid());

        try {
            $this->registration->register(
                $event,
                $payload,
                $sessionKey,
                $participantToken
            );
        } catch (CalculationException $exception) {
            Session::forget($sessionKey);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['registration' => $this->friendlyCalculationError($exception)]);
        }

        Session::forget($sessionKey);

        return redirect()->route('public.events.confirmed', [
            'slug' => $slug,
            'token' => $participantToken,
        ])->with([
            'participant_token' => $participantToken,
            'success' => true,
            'message' => 'Pendaftaran berhasil disimpan. Silakan lanjutkan pembayaran.',
        ]);
    }

    /**
     * Ubah exception perhitungan menjadi pesan yang mudah dipahami peserta.
     */
    private function friendlyCalculationError(CalculationException $exception): string
    {
        $message = $exception->getMessage();

        return match (true) {
            str_contains($message, 'Insufficient stock') => 'Maaf, stok add-on yang kamu pilih tidak mencukupi. Silakan kurangi jumlahnya dan coba lagi.',
            str_contains($message, 'negative') => 'Jumlah add-on tidak boleh kurang dari 0.',
            str_contains($message, 'Invalid voucher') => 'Kode voucher tidak valid. Periksa kembali kode yang kamu masukkan.',
            str_contains($message, 'usage limit') => 'Kode voucher sudah mencapai batas pemakaian.',
            str_contains($message, 'expired') => 'Kode voucher sudah kedaluwarsa.',
            str_contains($message, 'Ticket tier') => 'Tiket yang dipilih tidak tersedia. Silakan pilih tiket lain.',
            default => 'Terjadi kesalahan saat memproses pendaftaran. Silakan coba lagi.',
        };
    }

    public function confirmed(Request $request, string $slug): View
    {
        $token = $request->route('token');

        $registrant = $this->registration->getRegistrant($token);

        if (! $registrant) {
            return redirect()->route('public.events.index')->withErrors(['participant_token' => 'Token pendaftaran tidak valid.']);
        }

        $event = $registrant->event;

        return view('public.events.confirmed', [
            'registrant' => $registrant,
            'event' => $event,
            'token' => $token,
        ]);
    }
}
