<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shops = Shop::all();
        return response()->json($shops);
        //return view('shops', ['shops' => $shops]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = Shop::findOrFail($id);
        if($shop && $shop->is_active == 0){
            return redirect()->back()->with('error', 'Cette boutique n\'est plus disponible.');
        }

        $products = Product::where('shop_id', $id)->get();

        // Get current user information and membership status状态
        $current_user = Auth::user();
        if(env("APP_DEBUG")){
            $adhesion_valid = true;
        }else{
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }

        return view('shop.show', compact('shop', 'products', 'current_user', 'adhesion_valid'));
    }

    /**
     * Handle ticket purchase
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $product_id
     * @return \Illuminate\Http\Response
     */
    public function purchaseTicket(Request $request, $product_id)
    {
        /* à modifier implémenter une logique de tickets
            différente ou tout les tickets ne doivent pas
            être rentrés au préhalable dans l'application*/



        $validated = $request->validate([
            'regular_quantity' => 'required|integer|min:0',
            'subsidized_quantity' => 'required|integer|min:0',
        ]);

        if ($validated['regular_quantity'] == 0 && $validated['subsidized_quantity'] == 0) {
            return redirect()->back()->with('error', 'Veuillez sélectionner au moins un ticket.');
        }

        $product = Product::findOrFail($product_id);

        if ($product && $product->shop->is_active == 0) {
            return redirect()->back()->with('error', 'Ce produit n\'est plus disponible.');
        }

        $regularQuantity = $validated['regular_quantity'];
        $subsidizedQuantity = $validated['subsidized_quantity'];

        $regularTotal = $regularQuantity * $product->price;
        $subsidizedTotal = $subsidizedQuantity * $product->subsidized_price;
        $totalAmount = $regularTotal + $subsidizedTotal;

        $summary = [];
        if ($regularQuantity > 0) {
            $summary[] = "$regularQuantity ticket(s) au prix normal";
        }
        if ($subsidizedQuantity > 0) {
            $summary[] = "$subsidizedQuantity ticket(s) au prix subventionné";
        }
        $summaryText = implode(' et ', $summary);

        // ajouter la logique pour créer la commande ou rediriger vers HelloAsso

        return redirect()->back()->with('success', "Commande de $summaryText pour un total de $totalAmount € enregistrée. Redirection vers le système de paiement à implémenter.");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'min_limit' => 'nullable|numeric',
            'end_date' => 'nullable|date',
            'is_active' => 'boolean|nullable',
            'thumbnail' => 'nullable|string',
            'doc_link' => 'nullable|string',
            'bc_link' => 'nullable|string',
            'ha_link' => 'nullable|string',
            'photo_link' => 'nullable|string',
        ]);

        $shop = Shop::create($validated);
        return response()->json($shop, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        $validated = $request->validate([
            'shop_name' => 'sometimes|string|max:255',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
            'min_limit' => 'nullable|numeric',
            'end_date' => 'nullable|date',
            'is_active' => 'boolean|nullable',
            'thumbnail' => 'nullable|string',
            'doc_link' => 'nullable|string',
            'bc_link' => 'nullable|string',
            'ha_link' => 'nullable|string',
            'photo_link' => 'nullable|string',
        ]);

        $shop->update($validated);
        return response()->json($shop);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->delete();
        return response()->json(['message' => 'Shop deleted successfully']);
    }
}
