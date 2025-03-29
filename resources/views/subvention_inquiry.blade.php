@extends('template')

@section('title')
    Demande de Subvention Sportive
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/subvention_inquiry.css') }}">
    <script src="{{ asset('js/pages_js/subvention_inquiry.js') }}"></script>
@endsection

@section('content')
    <h3>Demande de Subvention Sportive</h3>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            {{ session('success') }}
        </div>
    @endif
    <div class="subvention-container">
        @if(!$last_pending_subvention)
            <form action="{{ route('subventions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3 form-floating form-field">
                    <input type="text" name="name_asso" id="name_asso" placeholder="Nom de l'association" class="form-control" value="{{ $prev_nom_asso }}" required>
                    <label for="name_asso">Nom association</label>
                    @error('name_asso')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-floating form-field">
                    <input type="text" placeholder="RIB" name="RIB" id="RIB" class="form-control" value="{{ $prev_rib }}" required>
                    <label for="RIB" >IBAN</label>
                </div>

                <div class="mb-3 form-floating form-field">
                    <input type="number" placeholder="montant" name="montant" id="montant" class="form-control disabled-input" value="20" readonly>
                    <label for="montant" >Montant demandé</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Soumettre la demande</button>
                </div>
            </form>
            <br><br>
        @endif
        @if($last_pending_subvention)
            <div class="pending-subvention-info">
                <h4>Votre demande de subvention en attente :</h4>
                <div class="mb-3 form-floating form-field">
                    <input type="text" class="form-control disabled-input" value="{{ $last_pending_subvention->name_asso }}" readonly>
                    <label>Nom de l'association</label>
                </div>
                <div class="mb-3 form-floating form-field">
                    <input type="text" class="form-control disabled-input" value="{{ $last_pending_subvention->RIB }}" readonly>
                    <label>IBAN</label>
                </div>
                <div class="mb-3 form-floating form-field">
                    <input type="text" class="form-control disabled-input" value="{{ $last_pending_subvention->montant }}" readonly>
                    <label>Montant demandé</label>
                </div>
            </div>
        @endif
    </div>
    <div class="previous-subvention-container">
        <div class="resolved-subvention-info">
            <h4>Subventions Précédentes</h4>
            @if(count($previous_subventions) > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom de l'association</th>
                            <th>RIB</th>
                            <th>Montant demandé</th>
                            <th>Date de résolution</th>
                            <th>Etat de la demande</th>
                            <th>Motif de refus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previous_subventions as $subvention)
                            <tr>
                                <td>{{ $subvention->name_asso }}</td>
                                <td>{{ $subvention->RIB }}</td>
                                <td>{{ $subvention->montant }}</td>
                                <td>{{ $subvention->payment_subvention ? $subvention->payment_subvention->format('d/m/Y') : 'Non payée' }}</td>
                                <td>{{ $subvention->state->label_state }}</td>
                                <td>{{ $subvention->motif_refus ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Aucune subvention traitée trouvée.</p>
            @endif
        </div>
    </div>
@endsection
