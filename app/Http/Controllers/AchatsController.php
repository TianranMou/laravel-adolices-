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


    /**
     * Display the list of aggregated tickets for the authenticated user.
     *
     * @return \Illuminate\View\View The view displaying the aggregated tickets and adhesion status.
     */
    public function index()
    {
        $user = auth()->user();
        $adhesion_valid = true; //debug

        $aggregatedTickets = Ticket::getAggregatedTicketsForUser($user->user_id);

        return view('achats', compact('aggregatedTickets', 'adhesion_valid'));
    }
}
