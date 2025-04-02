<?php

namespace App\Http\Controllers;
use App\Models\Shop;
use App\Models\Config;
use Illuminate\Support\Facades\Auth;
use App\Models\Adhesion;
use App\Models\User;

use Illuminate\Http\Request;

class AccueilPageController extends Controller
{
    /**
     * Afficher la page d'accueil avec les données nécessaires.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Variables temporaires de présentation

        $adhesion_link = route('adhesion');
        $presentation = Config::findByLabel('presentation')->config_value ?? '';
        $current_user = Auth::user();

        if(env("APP_DEBUG")){
            $adhesion_valid = true;
        }else{
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }
        // Données des shops
        $allShops = Shop::getAllAvalableShops();
        //$allShops = Shop::all();
        $shops = [];
        $billeteries = [];

        if ($allShops->isNotEmpty()) {
            foreach ($allShops as $key => $value) {
                if($value->end_date ){
                    $shops[$key] = $value;
                }
                else{
                    $billeteries[$key] = $value;
                }
            }
        }
        //dd($shops,$billeteries);

        return view('accueil', compact('adhesion_valid', 'current_user','adhesion_link', 'presentation', 'shops','billeteries'));
    }
}
