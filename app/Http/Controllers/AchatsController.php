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
        $adhesion_valid = true; //debug

        $aggregatedTickets = Ticket::getAggregatedTicketsForUser($user->user_id);

        return view('achats', compact('aggregatedTickets', 'adhesion_valid'));
    }
}
