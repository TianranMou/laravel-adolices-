<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BureauPageController extends Controller
{
    public function index()
    {
        // Définir les membres du bureau en tant que tableau associatif
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

        //à recup

        $current_user = Auth::user();
        if(env("APP_DEBUG")){
            $adhesion_valid = true;
        }else{
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }

        // Passer les données à la vue
        return view('Bureau', compact('bureau_data','adhesion_valid','current_user'));
    }
}
