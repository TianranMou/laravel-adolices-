@extends('template')

@section('title')
    Bureau
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/bureau.css') }}">
@endsection

@section('content')
    <div class="bureau-container">
        <h1>Les Membres du Bureau</h1>
        <div id="bureau-list">
            @foreach ($bureau_data as $member)
                <div class="bureau-member">
                    <img src="{{ asset('images/bureau/'.$member['photo']) }}" alt="{{ $member['name'] }}" class="member-photo">
                    <div class="member-info">
                        <h4>{{ $member['name'] }}</h4>
                        <p>{{ $member['role'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
