<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Shop, Administrator};

class ContactController extends Controller
{
    /**
     * Show the contact page
     */
    public function index()
    {
        $shops = Shop::all(['shop_id', 'shop_name']);
        return view('contact', compact('shops'));
    }

    /**
     * Get the administrators of a specific shop
     */
    public function getShopAdministrators($shopId)
    {
        $admins = Administrator::with(['user'])
            ->where('shop_id', $shopId)
            ->get()
            ->map(function($admin) {
                return [
                    'last_name' => $admin->user->last_name,
                    'first_name' => $admin->user->first_name,
                    'email' => $admin->user->email
                ];
            });

        return response()->json($admins);
    }

    /**
     * Get administrators from all shops
     */
    public function getAllAdministrators()
    {
        $admins = Administrator::with(['user', 'shop'])
            ->get()
            ->map(function($admin) {
                return [
                    'last_name' => $admin->user->last_name,
                    'first_name' => $admin->user->first_name,
                    'email' => $admin->user->email,
                    'shop_name' => $admin->shop->shop_name
                ];
            });

        return response()->json($admins);
    }
}
