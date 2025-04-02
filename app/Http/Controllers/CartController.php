<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function add(Request $request, Product $product)
    {
        // This method is kept for backward compatibility
        // but will be deprecated as we move to HelloAsso
        return redirect()->back()->with('info', 'Le système de panier est en cours de migration vers HelloAsso.');
    }
} 