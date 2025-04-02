@extends('template')

@section('title')
    Gestion des demandes de subventions sportives
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/subvention_management.css') }}">
    <script src="{{ asset('js/pages_js/subvention_management.js') }}"></script>
@endsection

@section('content')
    <h3>Gestion des demandes de subventions sportives</h3>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="inquiries-container">
        @forelse ($inquiries as $inquiry)
            <div class="inquiry-card">
                <button class="details-button" data-target="details-{{ $inquiry->subvention_id }}">
                    <div class="inquiry-header">
                        <h4>Demande de {{ $inquiry->user->last_name }} {{ $inquiry->user->first_name }}</h4>
                        <i class="fa fa-caret-down"></i>
                    </div>
                </button>
                <div id="details-{{ $inquiry->subvention_id }}" class="ticket-details">

                    <div class="pending-subvention-info">
                        <div class="mb-3 form-floating form-field">
                            <input type="text" class="form-control disabled-input" value="{{ $inquiry->name_asso }}" readonly>
                            <label>Nom de l'association</label>
                        </div>
                        <div class="mb-3 form-floating form-field">
                            <input type="text" class="form-control disabled-input" value="{{ $inquiry->RIB }}" readonly>
                            <label>IBAN</label>
                        </div>
                        <div class="mb-3 form-floating form-field">
                            <input type="text" class="form-control disabled-input" value="{{ $inquiry->montant }} €" readonly>
                            <label>Montant demandé</label>
                        </div>
                        @if($inquiry->link_attestation)
                            <div class="mb-3 form-field">
                                <p>Document joint : <a href="{{ route('demande-subvention.document.view', ['userId' => $inquiry->user_id, 'filename' => $inquiry->link_attestation]) }}" target="_blank">Voir le document</a></p>
                            </div>
                        @endif

                        <form action="{{ route('subventions.update', $inquiry) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div>
                                <label for="state_id" class="form-label mt-4">Statut</label>
                                <select class="form-select" name="state_id" id="state_id">
                                    @foreach(\App\Models\StateSub::all() as $state)
                                        <option value="{{ $state->state_id }}" {{ $inquiry->state_id == $state->state_id ? 'selected' : '' }}>{{ $state->label_state }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                            <label for="motif_refus" class="form-label mt-4">Motif de refus (si applicable)</label>
                            <textarea class="form-control" name="motif_refus" id="motif_refus" rows="3">{{ $inquiry->motif_refus }}</textarea>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p>Aucune demande de subvention en attente.</p>
        @endforelse
    </div>
@endsection
