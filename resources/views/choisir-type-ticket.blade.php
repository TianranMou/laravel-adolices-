@extends('template')

@php
    $admin_page = true;
@endphp

@section('title')
    Choisir Type Ticket
@endsection

@section('head')
    <link rel="stylesheet" href="{{ asset('css/pages_css/choisir_type_ticket.css') }}">
@endsection


@section('content')
<div class="container">
    <h4>Choisir le type de ticket</h4>
    <br>
    <form action="{{ route('tickets.redirect') }}" method="GET">
        <select id="ticket_type" name="ticket_type" class="form-select" required>
            <option value="majestic" >Ticket Majestic</option>
            <option value="standard" >Ticket Standard</option>
        </select>

        <input type="hidden" name="product_id" value="{{ $product_id }}">
        <!--
        <label>
            <input type="radio" name="ticket_type" value="majestic" required>
            Ticket Majestic
        </label>
        <br>

        <label>
            <input type="radio" name="ticket_type" value="standard" required>
            Ticket Standard
        </label>
-->
        <br>

        <button type="submit" class="btn btn-primary">Suivant</button>
    </form>
</div>
@endsection
