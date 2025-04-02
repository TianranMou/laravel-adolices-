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
     * @return \Illuminate\Http\RedirectResponse Redirects to the shop page with a success message.
     */
    public function update(Request $request, $shop_id)
    {
        // Validate the incoming data
        $request->validate([
            'shop_name' => 'required|string|max:250',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'min_limit' => 'nullable',
            'end_date' => 'nullable|date',
            'is_active' => 'required|boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'doc_link' => 'nullable|url',
            'bc_link' => 'nullable|url',
            'ha_link' => 'nullable|url',
            'photo_link' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Find the shop by its ID
        $boutique = \App\Models\Shop::findOrFail($shop_id);

        // Update the shop details
        $boutique->shop_name = $request->shop_name;
        $boutique->short_description = $request->short_description;
        $boutique->long_description = $request->long_description;

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

        // Save the updated shop details
        $boutique->save();

        // Redirect to the shop page with a success message
        return redirect()->route('produit.create', ['shop_id' => $shop_id])->with('success', 'La boutique a été mise à jour avec succès!');
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
