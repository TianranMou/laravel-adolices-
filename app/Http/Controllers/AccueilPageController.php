<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Config;
use Illuminate\Support\Facades\Auth;
use App\Models\Adhesion;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class AccueilPageController
 *
 * This controller handles the logic for displaying the homepage with the necessary data.
 */
class AccueilPageController extends Controller
{
    /**
     * Display the homepage with the required data.
     *
     * @return \Illuminate\View\View The view for the homepage.
     */
    public function index()
    {
        $adhesion_link = route('adhesion');

        $presentation = Config::findByLabel('presentation')->config_value ?? '';

        $current_user = Auth::user();

        // Determine if the user's adhesion is valid
        if (env("APP_DEBUG")) {
            $adhesion_valid = true; // Always valid in debug mode
        } else {
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }

        $allShops = Shop::getAllAvailableShops();

        $shops = [];
        $billeteries = [];

        if ($allShops->isNotEmpty()) {
            foreach ($allShops as $key => $value) {
                if ($value->end_date) {
                    $shops[$key] = $value; // Shops with an end date
                } else {
                    $billeteries[$key] = $value; // Permanent shops (billeteries)
                }
            }
        }

        return view('accueil', compact('adhesion_valid', 'current_user', 'adhesion_link', 'presentation', 'shops', 'billeteries'));
    }
}
