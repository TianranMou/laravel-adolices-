@extends('template')

@php
    $admin_page = true;
@endphp

@section('title')
    Ajouter un Ticket
@endsection

@php
    $admin_page = true;
@endphp

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/ajouter_ticket.css') }}">
@endsection

@section('content')

<form method="POST" action="{{ route('tickets.majestic', ['product_id' => $product_id]) }}" class="form-floating mb-3" enctype="multipart/form-data">
    @csrf
    <div class="upload-container">
        <h4>Sélectionnez des tickets Majestic à uploader</h4>
        <br>
        <div class="mb-3 form-floating">
            <input type="file" id="tickets" name="tickets[]" accept="application/pdf" class="form-floating mb-3" multiple required />
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </div>
</form>

@endsection





