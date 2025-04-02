<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => ['required'], // Removed 'email' validation, as it can be either email or email_imt
            'password' => ['required'],
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        // Attempt login with 'email' field
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        // Attempt login with 'email_imt' field
        if (Auth::attempt(['email_imt' => $email, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'password' => ['Les identifiants sont incorrects'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
