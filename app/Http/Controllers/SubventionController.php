<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Subvention;

class SubventionController extends Controller
{
    public function index()
    {
        $current_user = Auth::user();
        $prev_nom_asso = null;
        $prev_rib = null;
        $prev_montant = null;
        if(env("APP_DEBUG")){
            $adhesion_valid = true;
        }
        else{
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }

        $last_pending_subvention = Subvention::getLastPendingSubventionForUser($current_user->user_id);
        $previous_subventions = Subvention::getResolvedSubventionsForUser($current_user->user_id);
        return view('subvention_inquiry', compact('current_user', 'adhesion_valid',  'prev_nom_asso', 'prev_rib', 'prev_montant', 'last_pending_subvention', 'previous_subventions'));
    }

    public function store(Request $request)
    {
        $current_userId = Auth::id();
        $stateId = 1;
        $defaultAmount = 20;
        $validatedData = $request->validate([
            'name_asso' => 'required|string|max:255',
            'RIB' => 'required|string|max:255',
        ]);

        try {
            $subvention = Subvention::create([
                'user_id' => $current_userId,
                'name_asso' => $validatedData['name_asso'],
                'RIB' => $validatedData['RIB'],
                'montant' => $defaultAmount,
                'state_id' => $stateId,
            ]);
        } catch (\Exception $e) {
            \Log::error($e);
            $errorMessage = 'Une erreur s\'est produite lors de la soumission de la demande.';
            $errorId = '500';
            return view('error', compact('errorId', 'errorMessage'));
        }

        return redirect()->route('subventions.index')->with('subvention_success', 'Demande de subvention soumise avec succès !');
    }
}
