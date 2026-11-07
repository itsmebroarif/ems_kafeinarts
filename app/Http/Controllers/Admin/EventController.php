<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventBoardStatus;
use App\Enums\EventType;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Repositories\EventRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        private readonly EventRepository $repository,
    ) {}

    public function index(Request $request): View
    {
        $events = $this->repository->list($request->only(['search', 'status']));

        return view('admin.events.index', [
            'events' => $events,
        ]);
    }

    /**
     * Papan (kanban) event: kartu bisa dipindah antar kolom.
     */
    public function board(): View
    {
        $events = Event::query()
            ->with(['category', 'registrants'])
            ->orderByDesc('starts_at')
            ->get();

        $grouped = [];
        foreach (EventBoardStatus::ALL as $status) {
            $grouped[$status] = $events->filter(
                fn (Event $event) => $event->board_status === $status
            )->values();
        }

        return view('admin.events.board', [
            'columns' => EventBoardStatus::columns(),
            'grouped' => $grouped,
            'events' => $events,
        ]);
    }

    /**
     * Pindahkan kartu event ke kolom lain (drag & drop via AJAX).
     */
    public function moveBoard(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'board_status' => ['required', Rule::in(EventBoardStatus::ALL)],
        ]);

        $status = $validated['board_status'];

        if ($status === EventBoardStatus::RESULT && ! $event->isSport()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya event sport/esport (kategori Olahraga atau Esport) yang bisa dipindah ke kolom Result.',
            ], 422);
        }

        $event->board_status = $status;
        $event->save();

        return response()->json([
            'success' => true,
            'message' => "Event \"{$event->name}\" dipindah ke kolom \"".EventBoardStatus::label($status).'".',
            'board_status' => $status,
        ]);
    }

    /**
     * Menampilkan form menambah result event.
     */
    public function result(Request $request, Event $event): View
    {
        $categories = EventCategory::active()->get();

        return view('admin.events.result', [
            'event' => $event,
            'categories' => $categories,
        ]);
    }

    /**
     * Menyimpan result event.
     */
    public function resultStore(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:event_categories,id'],
            'board_status' => ['required', 'in:'.implode(',', EventBoardStatus::ALL)],
            'result_details' => ['required', 'string', 'max:500'],
            'final_score' => ['nullable', 'string', 'max:100'],
            'winner' => ['nullable', 'string', 'max:255'],
            'mvp' => ['nullable', 'string', 'max:255'],
        ]);

        // Pastikan kategori adalah sport agar result boleh dimasukkan
        $category = EventCategory::find($validated['category_id']);
        if ($category && ! $category->is_sport) {
            return back()
                ->withInput()
                ->withErrors(['category_id' => 'Hanya kategori sport/esport yang boleh dimasukkan ke kolom Result.']);
        }

        $event->category_id = $validated['category_id'];
        $event->board_status = $validated['board_status'];
        $event->result_details = $validated['result_details'];
        $event->final_score = $validated['final_score'] ?? null;
        $event->winner = $validated['winner'] ?? null;
        $event->mvp = $validated['mvp'] ?? null;
        $event->save();

        return redirect()
            ->route('admin.events.board')
            ->with('success', "Result event \"{$event->name}\" berhasil disimpan.");
    }

    public function create(): View
    {
        return view('admin.events.create', [
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:events,slug'],
            'description' => ['nullable', 'string'],
            'provinsi' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'starts_time' => ['nullable', 'date_format:H:i'],
            'ends_time' => ['nullable', 'date_format:H:i'],
            'venue_details' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'max_registrants' => ['nullable', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['numeric', 'min:0'],
            'submit_fee' => ['numeric', 'min:0'],
            'timezone' => ['integer', 'min:0'],
            'registrant_hours' => ['integer', 'min:0'],
            'delete_at' => ['integer', 'min:0'],
            'dudent_at' => ['integer', 'min:0'],
            'status' => ['string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'category_id' => ['nullable', 'integer', 'exists:event_categories,id'],
            'event_type' => ['required', 'string', Rule::in(EventType::ALL)],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        // Combine date and time fields into datetime
        $validated['starts_at'] = $this->combineDateTime($validated['starts_at'] ?? null, $validated['starts_time'] ?? null);
        $validated['ends_at'] = $this->combineDateTime($validated['ends_at'] ?? null, $validated['ends_time'] ?? null);
        unset($validated['starts_time'], $validated['ends_time']);

        $this->repository->create($this->storeMedia($request, $validated));

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dibuat.');
    }

    public function show(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
            'categories' => $this->categories(),
        ]);
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:events,slug,'.$event->id],
            'description' => ['nullable', 'string'],
            'provinsi' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'starts_time' => ['nullable', 'date_format:H:i'],
            'ends_time' => ['nullable', 'date_format:H:i'],
            'venue_details' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'max_registrants' => ['nullable', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'fee' => ['numeric', 'min:0'],
            'submit_fee' => ['numeric', 'min:0'],
            'timezone' => ['integer', 'min:0'],
            'registrant_hours' => ['integer', 'min:0'],
            'delete_at' => ['integer', 'min:0'],
            'dudent_at' => ['integer', 'min:0'],
            'status' => ['string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_logo' => ['boolean'],
            'remove_banner' => ['boolean'],
            'category_id' => ['nullable', 'integer', 'exists:event_categories,id'],
            'event_type' => ['required', 'string', Rule::in(EventType::ALL)],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        // Combine date and time fields into datetime
        $validated['starts_at'] = $this->combineDateTime($validated['starts_at'] ?? null, $validated['starts_time'] ?? null);
        $validated['ends_at'] = $this->combineDateTime($validated['ends_at'] ?? null, $validated['ends_time'] ?? null);
        unset($validated['starts_time'], $validated['ends_time']);

        $this->repository->update($event, $this->storeMedia($request, $validated, $event));

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }

    /**
     * Simpan unggahan avatar/banner ke disk publik, atau hapus bila diminta.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function storeMedia(Request $request, array $validated, ?Event $event = null): array
    {
        foreach (['logo', 'banner'] as $field) {
            if ($request->hasFile($field)) {
                $this->deleteStoredMedia($event?->{$field});
                $validated[$field] = $request->file($field)->store('events', 'public');

                continue;
            }

            if ($request->boolean("remove_{$field}")) {
                $this->deleteStoredMedia($event?->{$field});
                $validated[$field] = null;

                continue;
            }

            unset($validated[$field]);
        }

        return $validated;
    }

    private function deleteStoredMedia(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    /**
     * @return Collection<int, EventCategory>
     */
    private function categories(): Collection
    {
        return EventCategory::active()->orderBy('name')->get();
    }

    /**
     * Combine a date string and a time string into a single datetime string.
     */
    private function combineDateTime(?string $date, ?string $time): ?string
    {
        if ($date === null) {
            return null;
        }

        if ($time === null) {
            return $date;
        }

        return $date.' '.$time.':00';
    }
}
