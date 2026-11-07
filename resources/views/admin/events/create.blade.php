@extends('layouts.app')

@section('title', 'Buat Event')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            @include('admin.events.form-wizard', [
                'action' => route('admin.events.store'),
                'title' => 'Buat Event Baru',
                'event' => null,
                'submitLabel' => 'Simpan Event',
            ])
        </div>
    </div>
</div>
@endsection
