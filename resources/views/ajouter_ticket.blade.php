@extends('template')

@php
    $admin_page = true;
@endphp

@section('title')
    Ajouter un Ticket
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/ajouter_ticket.css') }}">
@endsection

@section('content')
    <form method="POST" action="{{ route('tickets.upload', ['product_id' => $product_id]) }}" class="form-floating mb-3" enctype="multipart/form-data">
        @csrf
        <div class="upload-container">
            <h4>Sélectionnez un type de ticket et téléchargez le fichier</h4>
            <br>

            <!-- Menu déroulant pour sélectionner le type de ticket -->
            <div class="mb-3">
                <label for="ticket_type" class="form-label">Type de ticket</label>
                <select class="form-select" id="ticket_type" name="ticket_type" required>
                    <option value="majestic">Ticket Majestic</option>
                    <option value="standard">Ticket Standard</option>
                </select>
            </div>

            <!-- Champ de téléchargement de fichier -->
            <div class="mb-3 form-floating">
                <input type="file" id="tickets" name="tickets[]" accept="application/pdf" class="form-floating mb-3" multiple required />
            </div>
            
            <div class="d-flex justify-content-between align-items-center">
                <!-- Bouton Retour aux boutiques à gauche -->
                <a href="{{ route('boutiques') }}" class="btn btn-secondary btn-sm">Retour aux boutiques</a>

                <!-- Bouton Ajouter à droite -->
                <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
            </div>
        </div>
    </form>
@endsection
