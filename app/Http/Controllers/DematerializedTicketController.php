<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Product;
use \App\Models\User;
use App\Models\Site;
use Illuminate\Http\Request;

class DematerializedTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::select('ticket.*')
            ->join('product', 'ticket.product_id', '=', 'product.product_id')
            ->where('product.dematerialized', true)
            ->with(['produit', 'user', 'site']);

        if ($request->has('user_id')) {
            $query->where('ticket.user_id', $request->input('user_id'));
        }

        $tickets = $query->get();

        return view('dematerialized-tickets.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = Ticket::select('ticket.*')
            ->join('product', 'ticket.product_id', '=', 'product.product_id')
            ->where('product.dematerialized', true)
            ->where('ticket.ticket_id', $id)
            ->with(['produit', 'user', 'site'])
            ->first();

        if (!$ticket) {
            abort(404, 'Dematerialized ticket not found.');
        }

        return view('dematerialized-tickets.show', compact('ticket'));
    }

    public function edit($id)
    {
        $ticket = Ticket::select('ticket.*')
            ->join('product', 'ticket.product_id', '=', 'product.product_id')
            ->where('product.dematerialized', true)
            ->where('ticket.ticket_id', $id)
            ->with(['produit', 'user', 'site'])
            ->first();

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
        $ticket = Ticket::select('ticket.*')
            ->join('product', 'ticket.product_id', '=', 'product.product_id')
            ->where('product.dematerialized', true)
            ->where('ticket.ticket_id', $id)
            ->first();

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