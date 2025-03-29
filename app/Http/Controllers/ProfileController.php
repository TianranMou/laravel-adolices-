<?php

namespace App\Http\Controllers;

use App\Models\{User, Status, Group, Adhesion,Site};
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
        $site = Site::all();

        return view('Profile', compact('current_user', 'adhesion_valid', 'statuses', 'groups','site'));
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Valider les champs du formulaire
        $request->validate([
            'status_id' => 'required|exists:status,status_id',
            'group_id' => 'required|exists:group,group_id',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'email_imt' => 'nullable|email|max:255|unique:users,email_imt,' . $user->user_id . ',user_id',
            'phone_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validation de la photo
        ]);

        // Mettre à jour les informations de l'utilisateur
        $user->status_id = $request->status_id;
        $user->group_id = $request->group_id;
        $user->last_name = $request->last_name;
        $user->first_name = $request->first_name;
        $user->email = $request->email;
        $user->email_imt = $request->email_imt;
        $user->phone_number = $request->phone_number;

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->move(public_path('userPhotos'), $fileName);

            // Enregistrer le chemin de la photo dans la base de données
            $user->photo = 'userPhotos/' . $fileName;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Update the user's profile photo
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        // Validate the uploaded file
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle the file upload
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('profile_photos', $fileName, 'public');

            // Update the user's photo path
            $user->photo = '/storage/' . $filePath;
            $user->save();
        }

        return redirect()->route('profile')->with('success', 'Photo de profil mise à jour avec succès.');
    }
}

