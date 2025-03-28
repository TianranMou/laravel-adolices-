<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use Illuminate\Support\Facades\Auth;

class AccueilController extends Controller
{
    public function index()
    {
        $presentation = Config::findByLabel('presentation')->config_value ?? 'Aucune présentation';
        $current_user = Auth::user();
        $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;


        return view('accueil', ['presentation' => $presentation, 'adhesion_valid' => $adhesion_valid]);
    }
}
