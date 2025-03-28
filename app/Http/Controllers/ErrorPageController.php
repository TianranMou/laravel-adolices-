<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorPageController extends Controller
{
    public function showError($errorId = '404')
    {
        // En fonction de l'ID d'erreur, tu peux définir un message spécifique
        $errorMessages = [
            '404' => "La page que vous recherchez n'existe pas.",
            '500' => "Une erreur interne du serveur est survenue.",
            '403' => "Accès interdit. Vous n'avez pas les autorisations nécessaires.",
            '418' => "I'm a teapot.",
            'default' => "Une erreur est survenue. Veuillez réessayer plus tard."
        ];

        // Si l'ID d'erreur n'est pas défini, on prend 'default'
        $errorMessage = $errorMessages[$errorId] ?? $errorMessages['404'];

        // Passer la variable d'erreur à la vue
        return view('error', compact('errorId', 'errorMessage'));
    }
}
