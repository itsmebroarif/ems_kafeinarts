@extends('layouts.app')

@section('title', 'Buat Event')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
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
