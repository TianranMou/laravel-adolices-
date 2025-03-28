<?php

namespace App\Http\Controllers;

use App\Models\StateSub;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StateSubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stateSubs = StateSub::all();
        return response()->json($stateSubs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'label_state' => 'required|string|max:255',
        ]);

        $stateSub = StateSub::create($validatedData);
        return response()->json($stateSub, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stateSub = StateSub::find($id);
        if (!$stateSub) {
            return response()->json(['message' => 'StateSub not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($stateSub);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $stateSub = StateSub::find($id);
        if (!$stateSub) {
            return response()->json(['message' => 'StateSub not found'], Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validate([
            'label_state' => 'required|string|max:255',
        ]);

        $stateSub->update($validatedData);
        return response()->json($stateSub);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $stateSub = StateSub::find($id);
        if (!$stateSub) {
            return response()->json(['message' => 'StateSub not found'], Response::HTTP_NOT_FOUND);
        }
        $stateSub->delete();
        return response()->json(['message' => 'StateSub deleted'], Response::HTTP_NO_CONTENT);
    }
}
