<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['produit', 'user', 'site'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $tickets,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:product,product_id',
            'user_id' => 'required|exists:users,user_id',
            'site_id' => 'required|exists:site,site_id',
            'ticket_link' => 'required|string|max:255',
            'partner_code' => 'required|string|max:255',
            'partner_id' => 'required|string|max:255', 
            'validity_date' => 'required|date',
            'purchase_date' => 'required|date',
        ]);

        $ticket = Ticket::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket created successfully.',
            'data' => $ticket->load(['produit', 'user', 'site']),
        ], 201);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['produit', 'user', 'site']);
        return response()->json([
            'status' => 'success',
            'data' => $ticket,
        ], 200);
    }


    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:product,product_id',
            'user_id' => 'required|exists:users,user_id',
            'site_id' => 'required|exists:site,site_id',
            'ticket_link' => 'required|string|max:255',
            'partner_code' => 'required|string|max:255',
            'partner_id' => 'required|string|max:255', 
            'validity_date' => 'required|date',
            'purchase_date' => 'required|date',
        ]);

        $ticket->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket updated successfully.',
            'data' => $ticket->load(['produit', 'user', 'site']),
        ], 200);
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket deleted successfully.',
        ], 204);
    }
}