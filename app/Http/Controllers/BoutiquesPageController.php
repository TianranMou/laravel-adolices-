<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;

/**
 * Class BoutiquesPageController
 *
 * This controller handles the logic for displaying the boutiques page.
 */
class BoutiquesPageController extends Controller
{
    /**
     * Display the boutiques page with the list of all boutiques and those managed by the current user.
     *
     * @return \Illuminate\View\View The view for the boutiques page.
     */
    public function index()
    {
        // Retrieve all boutiques
        $boutiques = Shop::all();

        // Get the currently authenticated user
        $currrentUser = auth()->user();

        // Retrieve the boutiques managed by the current user
        $BoutiquesGeredByUser = $currrentUser->getBoutiquesGerees();

        // Return the boutiques view with the required data
        return view('Boutiques', compact('boutiques', 'BoutiquesGeredByUser'));
    }
}
