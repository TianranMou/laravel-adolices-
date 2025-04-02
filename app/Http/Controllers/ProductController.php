<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // Afficher le formulaire d'ajout de produit
    public function create()
    {
        $products = Product::all(); // Récupérer tous les produits

        // Ajouter le nombre de tickets disponibles pour chaque produit
        $products->each(function($product) {
            $product->nbTickets = $product->nbTickets; // Accessor pour le nombre de tickets
        });

        return view('ajouter_produit' , compact('products'));
    }

    // Sauvegarder un produit en BDD
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'shop_id' => 'required|integer',
            'quota_id' => 'required|integer',
            'withdrawal_method' => 'required|string',
            'subsidized_price' => 'required|numeric',
            'dematerialized' => 'required|boolean',
            'price' => 'required|numeric',
        ]);

        $product = Product::create($validated);

        $product->nbTickets = $product->nbTickets; // Accessor pour le nombre de tickets

        return response()->json(['success' => true, 'product' => $product]);
    }

    public function getProduct($product_id)
    {
        $product = Product::where('product_id', $product_id)->first();

        if ($product) {
            $product->nbTickets = $product->nbTickets;  // Accessor pour obtenir le bon nombre de tickets
            return response()->json(['success' => true, 'product' => $product]);
        }

        return response()->json(['success' => false, 'message' => 'Produit non trouvé'], 404);
    }



}
