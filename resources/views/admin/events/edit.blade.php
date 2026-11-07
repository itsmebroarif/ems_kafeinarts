@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0 text-truncate">{{ $event->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-eye me-1"></i>Detail
            </a>
            <a href="{{ route('admin.events.tiers.index', $event->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-ticket-detailed me-1"></i>Kelola Event
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            @include('admin.events.form-wizard', [
                'action' => route('admin.events.update', $event->id),
                'title' => 'Edit Event',
                'event' => $event,
                'submitLabel' => 'Perbarui Event',
                'mode' => 'tabs',
            ])
        </div>
    </div>
</div>
@endsection
