<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;

class BoutiquesPageController extends Controller
{
    public function index()
    {
        $boutiques = Shop::all();
        return view('boutiques', compact('boutiques'));
    }
}
