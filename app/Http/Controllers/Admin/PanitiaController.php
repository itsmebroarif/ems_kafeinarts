<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PanitiaRole;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCommittee;
use App\Models\Payroll;
use App\Models\User;
use App\Services\Ticket\TicketGenerationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PanitiaController extends Controller
{
    public function __construct(
        private readonly TicketGenerationService $tickets,
    ) {}

    /**
     * Daftar panitia sebuah event beserta payroll dan totalnya.
     */
    public function index(Request $request, Event $event): View
    {
        $members = EventCommittee::with(['user', 'payrolls'])
            ->where('event_id', $event->id)
            ->get()
            ->sort(function (EventCommittee $a, EventCommittee $b) {
                $order = array_flip(PanitiaRole::ALL);

                return ($order[$a->role] ?? 99) <=> ($order[$b->role] ?? 99) ?: $a->id <=> $b->id;
            })
            ->values();

        $totalBase = (float) $members->sum(fn ($member) => $member->payrolls->sum('base_salary'));
        $totalNet = (float) $members->sum(fn ($member) => $member->payrolls->sum('net_salary'));
        $totalPaid = (float) $members->sum(fn ($member) => $member->payrolls->where('status', 'paid')->sum('net_salary'));

        // Pengguna yang belum ada di kepanitiaan event ini (untuk form tambah anggota).
        $availableUsers = User::query()
            ->whereDoesntHave('committees', fn ($q) => $q->where('event_id', $event->id))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.panitia.index', [
            'event' => $event,
            'members' => $members,
            'availableUsers' => $availableUsers,
            'roles' => PanitiaRole::options(),
            'totalBase' => $totalBase,
            'totalNet' => $totalNet,
            'totalPaid' => $totalPaid,
        ]);
    }

    /**
     * Tambahkan anggota kepanitiaan: akun yang sudah ada atau akun panitia baru.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $existingIds = EventCommittee::where('event_id', $event->id)->pluck('user_id');

        $validated = $request->validate([
            'mode' => ['required', Rule::in(['existing', 'new'])],
            'user_id' => ['nullable', 'required_if:mode,existing', 'integer', 'exists:users,id'],
            'role' => ['required', 'string', Rule::in(PanitiaRole::ALL)],
            'joined_at' => ['nullable', 'date'],
            // Mode akun baru
            'name' => ['nullable', 'required_if:mode,new', 'string', 'max:255'],
            'email' => ['nullable', 'required_if:mode,new', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'required_if:mode,new', 'string', 'max:30'],
            'password' => ['nullable', 'required_if:mode,new', 'string', 'min:8'],
        ]);

        if ($validated['mode'] === 'existing' && $existingIds->contains((int) $validated['user_id'])) {
            return back()->withErrors(['user_id' => 'Pengguna tersebut sudah ada di kepanitiaan event ini.']);
        }

        if ($validated['mode'] === 'existing') {
            $user = User::findOrFail($validated['user_id']);
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
            ]);
        }

        EventCommittee::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'role' => $validated['role'],
            'status' => 'aktif',
            'joined_at' => $validated['joined_at'] ?? now()->toDateString(),
        ]);

        return redirect()->route('admin.events.panitia.index', $event)->with('success', "Anggota {$user->name} berhasil ditambahkan sebagai ".PanitiaRole::label($validated['role']).'.');
    }

    /**
     * Ubah role anggota kepanitiaan.
     */
    public function updateRole(Request $request, Event $event, EventCommittee $member): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(PanitiaRole::ALL)],
        ]);

        $member->update(['role' => $validated['role']]);

        return back()->with('success', "Role {$member->user->name} diubah menjadi ".PanitiaRole::label($member->role).'.');
    }

    /**
     * Hapus anggota dari kepanitiaan (beserta payroll-nya).
     */
    public function destroy(Event $event, EventCommittee $member): RedirectResponse
    {
        $name = $member->user->name;
        $member->delete();

        return back()->with('success', "Anggota {$name} dihapus dari kepanitiaan.");
    }

    /**
     * Simpan/perbarui payroll seorang anggota.
     */
    public function payrollStore(Request $request, Event $event, EventCommittee $member): RedirectResponse
    {
        $validated = $request->validate([
            'base_salary' => ['required', 'numeric', 'min:0'],
            'allowance' => ['nullable', 'numeric', 'min:0'],
            'overtime' => ['nullable', 'numeric', 'min:0'],
            'deduction' => ['nullable', 'numeric', 'min:0'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date', 'after_or_equal:period_start'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $base = (float) $validated['base_salary'];
        $allowance = (float) ($validated['allowance'] ?? 0);
        $overtime = (float) ($validated['overtime'] ?? 0);
        $deduction = (float) ($validated['deduction'] ?? 0);

        $net = Payroll::computeNet($base, $allowance, $overtime, $deduction);

        $payroll = $member->payrolls()->latest('period_end')->first();

        $payload = [
            'base_salary' => $base,
            'allowance' => $allowance,
            'overtime' => $overtime,
            'deduction' => $deduction,
            'net_salary' => $net,
            'period_start' => $validated['period_start'] ?? null,
            'period_end' => $validated['period_end'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $payroll?->isPaid() ? 'paid' : 'draft',
        ];

        if ($payroll) {
            $payroll->update($payload);
        } else {
            $member->payrolls()->create($payload);
        }

        return back()->with('success', 'Payroll untuk '.$member->user->name.' tersimpan. Total bersih: Rp '.number_format($net, 0, ',', '.'));
    }

    /**
     * Tandai payroll anggota sebagai lunas (atau batalkan).
     */
    public function payrollPay(Request $request, Event $event, EventCommittee $member, Payroll $payroll): RedirectResponse
    {
        if ($payroll->status === 'paid') {
            $payroll->update(['status' => 'draft', 'paid_at' => null]);

            return back()->with('success', 'Status payroll '.$member->user->name.' dibatalkan (draft).');
        }

        $payroll->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', 'Payroll '.$member->user->name.' ditandai LUNAS.');
    }

    /**
     * Halaman ID Card panitia (siap print).
     */
    public function idCard(Event $event, EventCommittee $member): View
    {
        $payload = 'PANITIA|'.$event->id.'|'.$member->user_id.'|'.$member->role;
        $qr = $this->tickets->generateQrCode($payload);

        return view('admin.panitia.id-card', [
            'event' => $event,
            'member' => $member->load('user'),
            'qr' => $qr,
            'cardId' => 'KFT-'.strtoupper(substr(md5($event->id.$member->user_id.$member->id), 0, 10)),
        ]);
    }
}
