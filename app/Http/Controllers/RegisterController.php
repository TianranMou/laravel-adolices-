<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Status;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function index()
    {
        $statuses = Status::all();
        $groups = Group::all();
        $sites = \App\Models\Site::all(); // Récupérer tous les sites
        return view('register', compact('statuses', 'groups', 'sites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'status_id' => 'required|exists:status,status_id',
            'group_id' => 'required|exists:group,group_id',
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email',
                Rule::unique('users', 'email_imt')->ignore($request->email_imt, 'email'),
            ],
            'email_imt' => [
                'nullable',
                'email',
                'max:255',
                'unique:users,email_imt',
                Rule::unique('users', 'email')->ignore($request->email, 'email_imt'),
            ],
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:255',
            'site_ids' => 'nullable|array',
            'site_ids.*' => 'exists:site,site_id',
            'photo_release' => 'nullable|boolean',
            'photo_consent' => 'nullable|boolean',
        ]);

        if (empty($request->email) && empty($request->email_imt)) {
            throw ValidationException::withMessages([
                'email' => ['Au moins un email (personnel ou IMT) doit être renseigné.'],
            ]);
        }

        $user = User::create([
            'status_id' => $request->status_id,
            'group_id' => $request->group_id,
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'email_imt' => $request->email_imt,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'photo_release' => $request->has('photo_release') ? true : false,
            'photo_consent' => $request->has('photo_consent') ? true : false,
            'is_admin' => false,
        ]);

        // Associer les sites sélectionnés
        if ($request->has('site_ids')) {
            $user->sites()->sync($request->site_ids);
        }

        auth()->login($user);

        return redirect()->route('login')->with('success', 'Registration successful!');
    }
}
