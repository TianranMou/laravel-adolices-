<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;

class BoutiquesPageController extends Controller
{
    public function index()
    {
        $boutiques = Shop::all();
        $currrentUser = auth()->user();
        $BoutiquesGeredByUser = $currrentUser->getBoutiquesGerees();
        return view('Boutiques', compact('boutiques','BoutiquesGeredByUser'));
    }
}
