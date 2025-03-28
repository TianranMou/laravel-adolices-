<?php

namespace App\Http\Controllers;

use App\Models\Adhesion;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdhesionController extends Controller
{

    /**
     * Show the adhesion form
     */
    public function index()
    {
        $current_user = Auth::user();

        $adhesion_link = "https://www.helloasso.com/associations/adolices";
        if(env("APP_DEBUG")){
            $adhesion_valid = true;
        }else{
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }

        return view('adhesion', compact('adhesion_valid', 'adhesion_link','current_user'));
    }

    /**
     * Process the adhesion
     */
    public function createAdhesion(Request $request)
    {
        $user = auth()->user();
        $adhesion = Adhesion::createForUser($user->user_id);
        return redirect()->route('adhesion', ['adhesion_valid' => true]);
    }


    // unuse method for the moment
    /**
     * Get the list of adhesions for a specific user
     */
    public function getAdhesionsByUser($userId)
    {
        $adhesions = Adhesion::where('user_id', $userId)->get();
        return response(json_encode($adhesions), 200);
        //return views('...', ...);
    }
}
