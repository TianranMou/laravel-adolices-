@extends('template')

@section('title')
    Prévisualisation de l'email
@endsection

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('css/pages_css/email_preview.css')}}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="{{asset('js/pages_js/email_preview.js')}}" defer></script>
@endsection

@section('content')
    <div class="container mt-4">
        <h2>Prévisualisation de l'email</h2>

        <div class="mb-4">
            <button id="modifyEmailBtn" class="btn btn-primary">Modifier</button>
            <button id="sendEmailBtn" class="btn btn-success">Envoyer</button>
        </div>

        <div class="email-preview">
            <div class="email-header">
                <div>
                    <strong>Objet :</strong> {{ $subject }}
                </div>
                <div>
                    <strong>De :</strong> Adolices <noreply@adolices.fr>
                </div>
                <div>
                    <strong>À :</strong>
                    @foreach($recipients as $recipient)
                        {{ $recipient }}@if(!$loop->last), @endif
                    @endforeach
                </div>
            </div>

            <div class="email-content">
                {!! $content !!}
            </div>

            <div class="email-footer">
                Ceci est une prévisualisation. L'email sera envoyé après confirmation.
            </div>
        </div>
    </div>

    <!-- Modal de confirmation -->
    <div class="modal fade" id="confirmEmailModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmation d'envoi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir envoyer cet email ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <a href="{{ route('communiquer.confirm') }}" class="btn btn-success">Confirmer l'envoi</a>
                </div>
            </div>
        </div>
    </div>
@endsection
