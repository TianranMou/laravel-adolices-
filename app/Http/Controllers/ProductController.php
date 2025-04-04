<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function Symfony\Component\String\b;

/**
 * Class ProductController
 *
 * This controller handles the creation, management, and updates of shops and products.
 */
class ProductController extends Controller
{
    /**
     * Create a new shop (boutique) with default values and assign the current user as a gestionnaire.
     *
     * @return \Illuminate\Http\RedirectResponse Redirects to the shop creation page with a success message.
     */
    public function createBoutique()
    {
        // Create a new shop instance with default values
        $boutique = new \App\Models\Shop();
        $boutique->shop_name = 'Nouvelle Boutique'; // Default name
        $boutique->short_description = 'Description courte par défaut'; // Default short description
        $boutique->long_description = 'Description longue par défaut'; // Default long description
        $boutique->min_limit = 0; // Default minimum limit
        $boutique->end_date = null; // Default end date (none)
        $boutique->is_active = false; // Default inactive status
        $boutique->thumbnail = null; // Default thumbnail
        $boutique->doc_link = null; // Default document link
        $boutique->bc_link = null; // Default order form link
        $boutique->ha_link = null; // Default HelloAsso link
        $boutique->photo_link = null; // Default photo link

        // Save the new shop to the database
        $boutique->save();

        // Assign the current user as a gestionnaire
        $currentUser = auth()->user();
        $boutique->administrators()->attach($currentUser->user_id);

        // Redirect to the create function with the new shop ID
        return redirect()->action([self::class, 'create'], ['shop_id' => $boutique->shop_id])
            ->with('success', 'La boutique a été créée avec succès et vous avez été ajouté comme gestionnaire.');
    }

    /**
     * Display the form to add products to a shop.
     *
     * @param int $shop_id The ID of the shop.
     * @return \Illuminate\View\View The view for adding products.
     */
    public function create($shop_id)
    {
        $products = Product::where('shop_id', $shop_id)->get();
        $boutique = \App\Models\Shop::findOrFail($shop_id);
        $users = \App\Models\User::all(); // Retrieve all users

        return view('ajouter_produit', compact('products', 'shop_id', 'boutique', 'users'));
    }

    /**
     * Save a new product to the database.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     * @param int $shop_id The ID of the shop to which the product belongs.
     * @return \Illuminate\Http\JsonResponse A JSON response with the created product.
     */
    public function store(Request $request, $shop_id)
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

        // Add shop_id manually because it's not in the validated data
        $validated['shop_id'] = $shop_id;

        $product = Product::create($validated);

        $product->nbTickets = $product->nbTickets; // Accessor for the number of tickets

        return response()->json(['success' => true, 'product' => $product]);
    }

    public function updateProduct(Request $request, $shop_id)
    {
        $product_id = $request->input('product_id'); // Récupérer l'ID du produit depuis les données envoyées

        $product = Product::where('shop_id', $shop_id)->findOrFail($product_id);

        $product->update([
            'product_name' => $request->input('product_name'),
            'withdrawal_method' => $request->input('withdrawal_method'),
            'subsidized_price' => $request->input('subsidized_price'),
            'price' => $request->input('price'),
            'dematerialized' => $request->input('dematerialized'),
            'quota_id' => $request->input('quota_id')
        ]);

        // Calculer le nombre de tickets associés à ce produit après la mise à jour
        $nbTickets = $product->tickets()->count();

        // Ajouter le nombre de tickets à la réponse
        $product->nbTickets = $nbTickets;


        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    public function deleteProduct($product_id)
    {
        $product = Product::find($product_id);

        if ($product) {
            $product->delete();  // Supprimer le produit
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);  // Si le produit n'existe pas
    }

    /**
     * Retrieve a product by its ID.
     *
     * @param int $product_id The ID of the product.
     * @return \Illuminate\Http\JsonResponse A JSON response with the product details or an error message.
     */
    public function getProduct($product_id)
    {
        $product = Product::where('product_id', $product_id)->first();

        if ($product) {
            $product->nbTickets = $product->nbTickets;  // Accessor to get the correct number of tickets
            return response()->json(['success' => true, 'product' => $product]);
        }

        return response()->json(['success' => false, 'message' => 'Produit non trouvé'], 404);
    }

    /**
     * Update the details of a shop.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     * @param int $shop_id The ID of the shop to update.
     * @return \Illuminate\Http\JsonResponse A JSON response with success status and message.
     */
    public function update(Request $request, $shop_id)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'shop_name' => 'required|string|max:250',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'min_limit' => 'nullable|numeric',
            'end_date' => 'nullable|date',
            'is_active' => 'sometimes|boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'doc_link' => 'nullable|url',
            'bc_link' => 'nullable|url',
            'ha_link' => 'nullable|url',
            'photo_link' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        try {
            // Find the shop by its ID
            $boutique = \App\Models\Shop::findOrFail($shop_id);

            // Update the shop details with validated data
            $boutique->shop_name = $validated['shop_name'];
            $boutique->short_description = $validated['short_description'];
            $boutique->long_description = $validated['long_description'];

            // Utiliser array_key_exists pour vérifier si le champ est présent dans les données validées
            // même s'il a une valeur null
            if (array_key_exists('min_limit', $validated)) {
                $boutique->min_limit = $validated['min_limit'];
            }

            if (array_key_exists('end_date', $validated)) {
                $boutique->end_date = $validated['end_date'];
            }

            $boutique->is_active = $request->boolean('is_active');

            if (array_key_exists('doc_link', $validated)) {
                $boutique->doc_link = $validated['doc_link'];
            }

            if (array_key_exists('bc_link', $validated)) {
                $boutique->bc_link = $validated['bc_link'];
            }

            if (array_key_exists('ha_link', $validated)) {
                $boutique->ha_link = $validated['ha_link'];
            }

            // Handle image uploads if provided
            if ($request->hasFile('photo_link')) {
                if ($boutique->photo_link) {
                    Storage::delete('public/'.$boutique->photo_link);
                }
                $photoPath = $request->file('photo_link')->store('photos', 'public');
                $boutique->photo_link = $photoPath;
            }

            if ($request->hasFile('thumbnail')) {
                if ($boutique->thumbnail) {
                    Storage::delete('public/' . $boutique->thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
                $boutique->thumbnail = $thumbnailPath;
            }

            $boutique->save();
            return redirect()->route('produit.create', ['shop_id' => $shop_id])->with('success', 'La boutique a été mise à jour avec succès !');
        } catch (\Exception $e) {
            return redirect()->route('produit.create', ['shop_id' => $shop_id])->with('fail', 'Echec de la mise à jour de la boutique !');
        }
    }

    /**
     * Manage the gestionnaires (managers) for a shop.
     *
     * @param \Illuminate\Http\Request $request The incoming request.
     * @param int $shop_id The ID of the shop.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a success message.
     */
    public function manageGestionnaires(Request $request, $shop_id)
    {
        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,user_id',
        ]);

        $shop = \App\Models\Shop::findOrFail($shop_id);

        // Sync the administrators for the shop
        $shop->administrators()->sync($request->user_ids);

        return redirect()->back()->with('success', 'Les gestionnaires ont été mis à jour avec succès.');
    }
}
