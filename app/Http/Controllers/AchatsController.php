<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

class AchatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        //$adhesion_valid = $user->hasUpToDateAdhesion();
        //debug
        $adhesion_valid = true;
        
        $tickets = Ticket::getTicketByUserId($user->user_id)
            ->load(['produit', 'site'])
            ->sortByDesc('purchase_date');

        return view('achats', compact('tickets','adhesion_valid'));
    }
}