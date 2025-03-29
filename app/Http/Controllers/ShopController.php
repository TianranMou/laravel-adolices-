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
    // 获取商店信息
    $shop = Shop::findOrFail($id);
    
    // 获取该商店的所有商品
    $products = Product::where('shop_id', $id)->get();
    
    // 获取当前用户信息和会员状态
    $current_user = Auth::user();
    if(env("APP_DEBUG")){
        $adhesion_valid = true;
    }else{
        $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
    }
    
    // 返回视图，传递数据
    return view('shop.show', compact('shop', 'products', 'current_user', 'adhesion_valid'));
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
