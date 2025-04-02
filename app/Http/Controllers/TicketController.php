<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::getAllTicketsWithRelations();
        return response()->json([
            'status' => 'success',
            'data' => $tickets,
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $ticket = Ticket::createTicket($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Ticket created successfully.',
                'data' => $ticket->load(['produit', 'user', 'site']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(Ticket $ticket)
    {
        $ticket = Ticket::findTicketWithRelations($ticket->ticket_id);
        return response()->json([
            'status' => 'success',
            'data' => $ticket,
        ], 200);
    }

    public function update(Request $request, Ticket $ticket)
    {
        try {
            $ticket->updateTicket($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Ticket updated successfully.',
                'data' => $ticket->load(['produit', 'user', 'site']),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket deleted successfully.',
        ], 204);
    }

    public function create($product_id){
        // Vérifie si le produit "Ticket Cine Majestic" existe déjà
        $product = Product::firstOrCreate([
            'product_name' => 'Ticket Cine Majestic',
            'shop_id'=>1,// à modifier
            'quota_id'=>1,
            'withdrawal_method'=>'pickup',
            'subsidized_price' => 0,
            'dematerialized' => 0,
            'price' => 0] // Ajuste selon ton besoin
        );

        // Sauvegarder l'ID du produit dans la session
        session(['product_id' => $product->product_id]);

        //$tickets = Ticket::where('product_id', $product->id)->latest()->take(10)->get();
        return view('ajouter_ticket_majestic', compact('product_id'));
    }


    public function uploadTickets(Request $request, PdfParserService $pdfParser, $product_id)
    {

        $request->validate([
            'tickets.*' => 'required|mimes:pdf|max:2048',
        ]);


        $uploadedTickets = [];

        // Récupère l'ID du produit depuis la session
        $productId = $product_id;

        foreach ($request->file('tickets') as $file) {
            //Utilisation du parser pour extraire partner_id et partner_code
            $parsedData = $pdfParser->getText($file->getPathname());

            // Maintenant, tu peux extraire les valeurs du texte
            $info = explode("|",$parsedData);

            $validitydate = $info[0] ?? null;
            $partnerId = $info[1] ?? null;
            $partnerCode = $info[2] ?? null;

            $dt = DateTime::createFromFormat(' d/m/Y',$validitydate); //espace ajouté volontairement pour ajuster le parsing et utiliser le format date

            // Renommage du fichier avec PartnerID
            $fileName = $partnerId . '.pdf';
            $path = $file->storeAs('tickets', $fileName, 'public');

            //Storage::disk('public')->put('tickets', $request->file('tickets'));

            // Enregistrement en BDD
            Ticket::create([
                'product_id' => $productId,
                'user_id' => 1, // amodifier car acheteur
                'site_id' => 1, //a modifier en fonction du site voulu
                'ticket_link' => $path,
                'partner_code' => $partnerCode,
                'partner_id' => $partnerId,
                'validity_date' => $dt,
                'purchase_date' => null,
                'created_at'=> now(),
                'updated_at'=> now()
            ]);

            $uploadedTickets[] = $fileName;
        }

        return back()->with('success', 'Tickets ajoutés avec succès : ' . implode(', ', $uploadedTickets));
    }

    public function viewTicket(Request $request, $ticketId)
    {

        $ticket = Ticket::find($ticketId);
        if (!$ticket) {
            $errorMessage = 'Ticket inconnu';
            $errorId = '404';
            return view('error', compact('errorId', 'errorMessage'));
        }
        if (!$ticket->ticket_link) {
            $errorMessage = 'Ticket non disponible';
            $errorId = '404';
            return view('error', compact('errorId', 'errorMessage'));
        }

        $currentUser = Auth::user();
        if ($currentUser->user_id != $ticket->user_id && !$currentUser->is_admin) {
            $errorMessage = 'Accès non autorisé.';
            $errorId = '403';
            return view('error', compact('errorId', 'errorMessage'));
        }

        $filePath = $ticket->ticket_link;

        if (!Storage::disk('public')->exists($filePath)) {
            $errorMessage = 'Ticket inconnu '.$filePath;
            $errorId = '404';
            return view('error', compact('errorId', 'errorMessage'));
        }

        $file = Storage::disk('public')->get($filePath);
        $mimeType = Storage::disk('public')->mimeType($filePath);

        return new StreamedResponse(function () use ($filePath) {
            $stream = Storage::disk('public')->readStream($filePath);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filePath . '"',
        ]);
    }
}
