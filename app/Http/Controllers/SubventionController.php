<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Subvention;
use App\Models\StateSub;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubventionController extends Controller
{
    /**
     * Display the subvention inquiry form.
     */
    public function index()
    {
        $current_user = Auth::user();
        $prev_nom_asso = null;
        $prev_rib = null;
        $prev_montant = null;
        // Check if the user has an up-to-date adhesion
        $adhesion_valid = env("APP_DEBUG") ? true : ($current_user ? $current_user->hasUpToDateAdhesion() : false);

        // Retrieve user's previous and pending subventions
        $last_pending_subvention = Subvention::getLastPendingSubventionForUser($current_user->user_id);
        $previous_subventions = Subvention::getResolvedSubventionsForUser($current_user->user_id);

        $pdfExists = Storage::disk('public')->exists('documents/reglement_interieur.pdf');
        $last_pending_subvention = Subvention::getLastPendingSubventionForUser($current_user->user_id);
        $previous_subventions = Subvention::getResolvedSubventionsForUser($current_user->user_id);
        return view('subvention_inquiry', compact('current_user', 'adhesion_valid',  'prev_nom_asso', 'prev_rib', 'prev_montant', 'last_pending_subvention', 'previous_subventions', 'pdfExists'));
    }

    /**
     * Download the attestation in PDF.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download()
    {
        $path = 'documents/attestation_sportive.pdf';

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Le fichier PDF du règlement intérieur n\'a pas été trouvé.');
        }

        return Storage::disk('public')->download($path, 'Attestation activité sportive ou culturelle ADOLICES.pdf');
    }

    /**
     * Handle the submission of a new subvention request.
     */
    public function store(Request $request)
    {
        $current_userId = Auth::id();
        $stateId = 1;  // Default state (e.g., pending)
        $defaultAmount = 20;

        // Validate input data
        $validatedData = $request->validate([
            'name_asso' => 'required|string|max:255',
            'RIB' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,doc,docx|max:2048', // Restrict file types
        ]);

        try {
            // Handle file upload
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = time() . '_' . $file->getClientOriginalName();
                $userFolder = $current_userId . '/subvention_documents'; // Create user-specific folder
                $path = Storage::disk('public')->putFileAs($userFolder, $file, $filename); // Store the file

                $validatedData['document'] = $filename;
            }

            // Save subvention request in the database
            Subvention::create([
                'user_id' => $current_userId,
                'name_asso' => $validatedData['name_asso'],
                'RIB' => $validatedData['RIB'],
                'montant' => $defaultAmount,
                'state_id' => $stateId,
                'link_attestation' => $validatedData['document'],
            ]);
        } catch (\Exception $e) {
            \Log::error($e); // Log errors for debugging
            $errorMessage = 'Une erreur s\'est produite lors de la soumission de la demande.';
            $errorId = '500';
            return view('error', compact('errorId', 'errorMessage'));
        }

        return redirect()->route('demande-subvention.index')->with(
            'subvention_success', 'Demande de subvention soumise avec succès !'
        );
    }

    /**
     * Display a stored document if the user has permission.
     */
    public function viewDocument(Request $request, $userId, $filename)
    {
        $currentUser = Auth::user();

        // Ensure the user is authorized to view this document
        if ($currentUser->user_id != $userId && !$currentUser->is_admin) {
            return view('error', [
                'errorId' => '403',
                'errorMessage' => 'Accès non autorisé.'
            ]);
        }

        $filePath = $userId . '/subvention_documents/' . $filename;

        // Check if file exists in storage
        if (!Storage::disk('public')->exists($filePath)) {
            return view('error', [
                'errorId' => '404',
                'errorMessage' => 'Document inconnu'
            ]);
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
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Display all pending subvention requests for admin review.
     */
    public function indexPending()
    {
        $inquiries = Subvention::pending()->orderBy('subvention_id', 'desc')->get();
        return view('subvention_management', compact('inquiries'));
    }

    /**
     * Update the status of a subvention request.
     */
    public function update(Request $request, Subvention $inquiry)
    {
        // Validate input data
        $request->validate([
            'state_id' => 'required|exists:state_sub,state_id',
            'motif_refus' => 'nullable|string|max:255',
        ]);

        $state = StateSub::find($request->state_id);
        $payment_subvention = ($state->state_id != 1) ? now() : null; // Set payment date if approved

        // Update subvention details
        $inquiry->update([
            'state_id' => $request->state_id,
            'motif_refus' => $request->motif_refus,
            'payment_subvention' => $payment_subvention,
        ]);

        return redirect()->back()->with(
            'success', 'Demande de subvention mise à jour avec succès.'
        );
    }
}
