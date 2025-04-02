@extends('template')

@section('title')
    Demande de subvention sportive
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/subvention_inquiry.css') }}">
    <script src="{{ asset('js/pages_js/subvention_inquiry.js') }}"></script>
@endsection

@section('content')
    <h3>Demande de subvention sportive</h3>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            {{ session('success') }}
        </div>
    @endif
    <div class="subvention-container">
        @if(!$last_pending_subvention)
            <div id="reglement-container">
                @if(isset($pdfExists) && $pdfExists)
                    <div class="download-button text-center">
                        <a href="{{ route('demande-subvention.download') }}" class="btn btn-primary">
                            <i class="fas fa-download"></i> Télécharger le justificatif
                        </a>
                    </div>
                @endif
            </div>
            <br>
            <form action="{{ route('demande-subvention.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3 form-floating form-field">
                    <input type="text" name="name_asso" id="name_asso" placeholder="Nom de l'association" class="form-control" value="{{ $prev_nom_asso }}" required>
                    <label for="name_asso">Nom de l'association</label>
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
                    <label for="montant">Montant demandé (€)</label>
                </div>

                <div class="mb-3 form-field">
                    <label for="document" class="form-label">Document justificatif</label>
                    <input type="file" name="document" id="document" class="form-control" required>
                    @error('document')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" {{ isset($sub_available) && !$sub_available ? 'disabled' : '' }}>
                        Soumettre la demande
                    </button>
                    @if(isset($sub_available) && !$sub_available)
                        <div class="alert alert-info mt-2">
                            <small>Vous avez déjà reçu une subvention pour cette année. Vous ne pouvez pas soumettre une nouvelle demande.</small>
                        </div>
                    @endif
                </div>
            </form>
            <br><br>
        @endif
        @if($last_pending_subvention)
            <div class="pending-subvention-info">
                <h4 class="text-center">Votre demande de subvention est en attente&nbsp;:</h4>
                <div class="mb-3 form-floating form-field">
                    <input type="text" class="form-control disabled-input" value="{{ $last_pending_subvention->name_asso }}" readonly>
                    <label>Nom de l'association</label>
                </div>
                <div class="mb-3 form-floating form-field">
                    <input type="text" class="form-control disabled-input" value="{{ $last_pending_subvention->RIB }}" readonly>
                    <label>IBAN</label>
                </div>
                <div class="mb-3 form-floating form-field">
                    <input type="text" class="form-control disabled-input" value="{{ $last_pending_subvention->montant }} €" readonly>
                    <label>Montant demandé</label>
                </div>
                @if($last_pending_subvention->link_attestation)
                    <div class="mb-3 form-field">
                        <p>Document joint : <a href="{{ route('demande-subvention.document.view', ['userId' => $current_user->user_id, 'filename' => $last_pending_subvention->link_attestation]) }}" target="_blank">{{ $last_pending_subvention->link_attestation }}</a></p>
                    </div>
                @endif
            </div>
        @endif
    </div>
    <div class="previous-subvention-container">
        <div class="resolved-subvention-info">
            <h4>Subventions précédentes</h4>
            @if(count($previous_subventions) > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom de l'association</th>
                            <th>Montant demandé</th>
                            <th>Date de traitement</th>
                            <th>Etat de la demande</th>
                            <th>Motif de refus</th>
                            <th>Document</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previous_subventions as $subvention)
                            <tr>
                                <td>{{ $subvention->name_asso }}</td>
                                <td>{{ $subvention->montant }}€</td>
                                <td>{{ $subvention->payment_subvention ? $subvention->payment_subvention->format('d/m/Y') : 'Non payée' }}</td>
                                <td>{{ $subvention->state->label_state }}</td>
                                <td>{{ $subvention->motif_refus ?? '-' }}</td>
                                <td>
                                    @if($subvention->link_attestation)
                                        <a href="{{ route('demande-subvention.document.view', ['userId' => $current_user->user_id, 'filename' => $subvention->link_attestation]) }}" target="_blank">{{ $subvention->link_attestation }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
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
