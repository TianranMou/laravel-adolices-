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
        $sites = Site::all();
        $current_user_sites = $current_user->sites->pluck('site_id')->toArray();
        return view('Profile', compact('current_user', 'adhesion_valid', 'statuses', 'groups','sites','current_user_sites'));
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
            'site_ids' => 'nullable|array', // Validation pour plusieurs sites
            'site_ids.*' => 'exists:site,site_id', // Vérifie que chaque site existe
            'photo_release' => 'nullable|boolean', // Validation pour l'autorisation de diffusion
            'photo_consent' => 'nullable|boolean', // Validation pour le consentement
        ]);

        try {
            // Mettre à jour les informations de l'utilisateur
            $user->status_id = $request->status_id;
            $user->group_id = $request->group_id;
            $user->last_name = $request->last_name;
            $user->first_name = $request->first_name;
            $user->email = $request->email;
            $user->email_imt = $request->email_imt;
            $user->phone_number = $request->phone_number;

            // Mettre à jour les autorisations
            $user->photo_release = $request->has('photo_release') ? $request->photo_release : false;
            $user->photo_consent = $request->has('photo_consent') ? $request->photo_consent : false;

            // Gérer les sites sélectionnés
            if ($request->has('site_ids')) {
                $user->sites()->sync($request->site_ids); // Synchronise les sites sélectionnés
            }

            // Gérer l'upload de la photo
            if ($request->hasFile('photo')) {
                // Supprimer l'ancienne photo si elle existe
                if ($user->photo) {
                    $oldPhotoPath = public_path($user->photo);
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                // Enregistrer la nouvelle photo
                $file = $request->file('photo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->move(public_path('userPhotos'), $fileName);

                // Enregistrer le chemin de la photo dans la base de données
                $user->photo = 'userPhotos/' . $fileName;
            }

            $user->save();

            // Retourner un message de succès
            return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
        } catch (\Exception $e) {
            // Retourner un message d'erreur en cas d'exception
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour du profil.');
        }
    }
}

