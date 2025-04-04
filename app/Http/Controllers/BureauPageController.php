<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class BureauPageController
 *
 * This controller handles the logic for displaying the bureau (board members) page.
 */
class BureauPageController extends Controller
{
    /**
     * Display the bureau page with the list of board members and user adhesion status.
     *
     * @return \Illuminate\View\View The view for the bureau page.
     */
    public function index()
    {
        // Modifier le code ou mettre dans la config
        $bureau_data = [
            [
                'name' => 'Jean Dupont',
                'role' => 'Président',
                'photo' => 'jean.jpeg'
            ],
            [
                'name' => 'Marie Martin',
                'role' => 'Vice-présidente',
                'photo' => 'marie.jpg'
            ],
            [
                'name' => 'Paul Jurand',
                'role' => 'Secrétaire',
                'photo' => 'paul.jpg'
            ],
            [
                'name' => 'Lucie Lemoine',
                'role' => 'Trésorière',
                'photo' => 'lucie.jpg'
            ],
            [
                'name' => 'Antoine Lefevre',
                'role' => 'Responsable Communication',
                'photo' => 'antoine.jpg'
            ]
        ];

        $current_user = Auth::user();

        // Determine if the user's adhesion is valid
        if (env("APP_DEBUG")) {
            $adhesion_valid = true; // Always valid in debug mode
        } else {
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }

        return view('Bureau', compact('bureau_data', 'adhesion_valid', 'current_user'));
    }
}
