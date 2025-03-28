<?php

namespace App\Http\Controllers; // No more Api subdirectory

use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FamilyMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $familyMembers = FamilyMember::all();
        return response()->json($familyMembers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'name_member' => 'required|string|max:255',
            'birth_date_member' => 'required|date',
            'first_name_member' => 'required|string|max:255',
            'relation' => 'required|string|max:255',
        ]);

        $familyMember = FamilyMember::create($validatedData);

        return response()->json($familyMember, Response::HTTP_CREATED); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $familyMember = FamilyMember::find($id);

        if (!$familyMember) {
            return response()->json(['message' => 'Family member not found'], Response::HTTP_NOT_FOUND); // 404 Not Found
        }

        return response()->json($familyMember);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $familyMember = FamilyMember::find($id);

        if (!$familyMember) {
            return response()->json(['message' => 'Family member not found'], Response::HTTP_NOT_FOUND); // 404 Not Found
        }

        $validatedData = $request->validate([
            'user_id' => 'sometimes|required|exists:users,user_id',
            'name_member' => 'sometimes|required|string|max:255',
            'birth_date_member' => 'sometimes|required|date',
            'first_name_member' => 'sometimes|required|string|max:255',
            'relation' => 'sometimes|required|string|max:255',
        ]);

        $familyMember->update($validatedData);

        return response()->json($familyMember);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $familyMember = FamilyMember::find($id);

        if (!$familyMember) {
            return response()->json(['message' => 'Family member not found'], Response::HTTP_NOT_FOUND); // 404 Not Found
        }

        $familyMember->delete();

        return response()->json(['message' => 'Family member deleted'], Response::HTTP_NO_CONTENT); // 204 No Content
    }
}