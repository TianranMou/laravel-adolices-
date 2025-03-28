<?php

namespace App\Http\Controllers;

use App\Models\{User, Status, Group, Adhesion};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};

class ProfileController extends Controller
{
    /**
     * Show a user profile page
     */
    public function index()
    {
        $current_user = auth()->user();
        $adhesion_valid = Adhesion::isValid($current_user->user_id);
        $statuses = Status::all();
        $groups = Group::all();

        return view('Profile', compact('current_user', 'adhesion_valid', 'statuses', 'groups'));
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validation des champs
        $request->validate([
            'status_id' => 'required|exists:status,status_id',
            'group_id' => 'required|exists:group,group_id',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'email_imt' => 'nullable|email|max:255|unique:users,email_imt,' . $user->user_id . ',user_id',
            'phone_number' => 'nullable|string|max:255',
            // Pas besoin de valider les booleans car ce sont des cases à cocher (checkboxes)
        ]);

        // Mettre à jour les informations de l'utilisateur
        $user->update([
            'status_id' => $request->status_id,
            'group_id' => $request->group_id,
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'email_imt' => $request->email_imt,
            'phone_number' => $request->phone_number,
            'photo_release' => $request->has('photo_release'),  // Si la case est cochée, le champ sera 'true'
            'photo_consent' => $request->has('photo_consent'),  // Idem
        ]);

        return redirect()->route('profile')->with('success', 'Profil mis à jour avec succès !');
    }


}

