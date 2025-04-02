<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorPageController extends Controller
{
    public function showError($errorId = '404')
    {
        // En fonction de l'ID d'erreur, tu peux définir un message spécifique
        $errorMessages = [
            '400' => "Requête invalide. Veuillez vérifier votre demande.",
            '401' => "Non autorisé. Veuillez vous authentifier.",
            '403' => "Accès interdit. Vous n'avez pas les autorisations nécessaires.",
            '404' => "La page que vous recherchez n'existe pas.",
            '408' => "Temps d'attente de la requête dépassé.",
            '418' => "I'm a teapot.",
            '429' => "Trop de requêtes. Veuillez réessayer plus tard.",
            '500' => "Une erreur interne du serveur est survenue.",
            '502' => "Mauvaise passerelle.",
            '503' => "Service indisponible. Veuillez réessayer plus tard.",
            '504' => "Temps d'attente de la passerelle dépassé.",
            'default' => "Une erreur est survenue. Veuillez réessayer plus tard."
        ];

        // Si l'ID d'erreur n'est pas défini, on prend 'default'
        $errorMessage = $errorMessages[$errorId] ?? $errorMessages['404'];

        // Passer la variable d'erreur à la vue
        return view('error', compact('errorId','errorMessage'));
    }
}
