<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Services\PdfParserService;
use DateTime;


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

    /**
     * Crée un nouveau ticket et renvoie les données en JSON.
     */
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

    /**
     * Affiche un ticket spécifique avec ses relations.
     */
    public function show(Ticket $ticket)
    {
        $ticket = Ticket::findTicketWithRelations($ticket->ticket_id);
        return response()->json([
            'status' => 'success',
            'data' => $ticket,
        ], 200);
    }

    /**
     * Met à jour un ticket existant.
     */
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

    /**
     * Supprime un ticket.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Ticket deleted successfully.',
        ], 204);
    }

    /**
     * Affiche la vue pour ajouter un ticket à un produit donné.
     */
    public function create($product_id){
        // Vérifie si le produit "Ticket Cine Majestic" existe déjà et le créé sinon
        $product = Product::firstOrCreate([
            'product_name' => 'Ticket Cine Majestic',
            'shop_id'=>1, // amodifier car acheteur, mettre nullable dans la BDD et ajouter lorsque le ticket est acheter
            'quota_id'=>1, //a modifier en fonction du site voulu (éventuellement un select)
            'withdrawal_method'=>'pickup',
            'subsidized_price' => 0,
            'dematerialized' => 0,
            'price' => 0] 
        );
        session(['product_id' => $product->product_id]);

        return view('ajouter_ticket', compact('product_id'));
    }

    /**
     * Télécharge et stocke plusieurs tickets PDF tout en les analysant.
     */
    public function uploadTickets(Request $request, PdfParserService $pdfParser, $product_id)
    {

        $request->validate([
            'tickets.*' => 'required|mimes:pdf|max:2048',
        ]);
        $uploadedTickets = [];
        $ticketType = $request->input('ticket_type'); // Récupérer le type de ticket

        foreach ($request->file('tickets') as $file) {

            // Utiliser un parsing différent en fonction du type de ticket
            //Possibilité d'ajouter des conditions pour d'autres types de tickets
            if ($ticketType == 'majestic') {
                $parsedData = $pdfParser->getTextMajestic($file->getPathname());
            } else {
                $parsedData = $pdfParser->getTextForStandard($file->getPathname());
            }
            
            $info = explode("|",$parsedData); //séparateur entre les informations parsées dans le ticket

            //Récupération des informations sous forme de tableau
            $validitydate = $info[0] ?? null;
            $partnerId = $info[1] ?? null;
            $partnerCode = $info[2] ?? null;

            $dt = DateTime::createFromFormat(' d/m/Y',$validitydate); //espace ajouté volontairement pour ajuster le parsing et utiliser le format date

            // Renommage du fichier avec PartnerID
            $fileName = $partnerId . '.pdf';
            $path = $file->storeAs('tickets', $fileName, 'public');

            // Enregistrement en BDD (logique à séparer entre le modèle et le contrôleur si voulu)
            Ticket::create([
                'product_id' => $product_id,
                'user_id' => 1, // amodifier car acheteur, mettre nullable dans la BDD et ajouter lorsque le ticket est acheter
                'site_id' => 1, //a modifier en fonction du site voulu (éventuellement un select)
                'ticket_link' => $path,
                'partner_code' => $partnerCode,
                'partner_id' => $partnerId,
                'validity_date' => $dt,
                'purchase_date' => null, // à update lors de l'achat
                'created_at'=> now(),
                'updated_at'=> now() // à update lors de l'achat
            ]);

            $uploadedTickets[] = $fileName;
        }

        return redirect()->route('tickets.majestic', ['product_id' => $product_id])
            ->with('success', 'Tickets correctement téléchargés.')
            ->with('delay', true);

    }

    /**
     * Permet à l'utilisateur de visualiser un ticket.
     */
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
