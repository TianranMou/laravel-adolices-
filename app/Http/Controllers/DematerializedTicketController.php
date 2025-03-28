<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Product;
use App\Models\User;
use App\Models\Site;
use Illuminate\Http\Request;

class DematerializedTicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); 
    }

    public function index(Request $request)
    {
        $user_id = $request->input('user_id');
        $tickets = Ticket::getDematerializedTickets($user_id);

        return view('dematerialized-tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::getDematerializedTicketById($id);

        if (!$ticket) {
            abort(404, 'Dematerialized ticket not found.');
        }

        return view('dematerialized-tickets.show', compact('ticket'));
    }

    public function edit($id)
    {
        $ticket = Ticket::getDematerializedTicketById($id);

        if (!$ticket) {
            abort(404, 'Dematerialized ticket not found.');
        }

        $users = User::all();
        $products = Product::where('dematerialized', true)->get();
        $sites = Site::all();

        return view('dematerialized-tickets.edit', compact('ticket', 'users', 'products', 'sites'));
    }

    public function update(Request $request, $id)
    {
        $ticket = Ticket::getDematerializedTicketById($id);

        if (!$ticket) {
            abort(404, 'Dematerialized ticket not found.');
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'product_id' => 'required|exists:product,product_id',
            'site_id' => 'required|exists:site,site_id',
            'ticket_link' => 'required|string|max:255',
            'partner_code' => 'required|string|max:255',
            'partner_id' => 'required|string|max:255',
            'validity_date' => 'required|date',
            'purchase_date' => 'required|date',
        ]);

        $product = Product::find($validated['product_id']);
        if (!$product || !$product->dematerialized) {
            return redirect()->back()->withErrors(['product_id' => 'The selected product is not dematerialized.']);
        }

        $ticket->update($validated);

        return redirect()->route('dematerialized-tickets.show', $ticket->ticket_id)
            ->with('success', 'Dematerialized ticket updated successfully.');
    }
}