<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Un lien de réinitialisation a été envoyé à votre adresse e-mail.');
        } else {
            return back()->withErrors(['email' => 'Une erreur est survenue lors de l\'envoi du lien de réinitialisation.'])->withInput();
        }
    }
}
