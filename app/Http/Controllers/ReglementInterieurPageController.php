<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReglementInterieurPageController extends Controller
{
    /**
     * Afficher la page du règlement intérieur.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Contenu du règlement intérieur (à recup de config)
        $reglement = "
            <p style='color:red; font-size:xx-large'>exemple</p>
            <h3>Règlement Intérieur de l'Association ADOLICES</h3>
            <p>Ce règlement intérieur a pour objectif d'encadrer les activités et le fonctionnement de l'association.</p>
            <h4>Article 1 : Adhésion</h4>
            <p>Toute personne souhaitant devenir membre de l'association doit remplir une demande d'adhésion et s'acquitter de la cotisation annuelle.</p>
            <h4>Article 2 : Cotisations</h4>
            <p>La cotisation annuelle est fixée à 10€. Elle est due pour chaque membre actif ou retraité.</p>
            <h4>Article 3 : Droits et devoirs des membres</h4>
            <p>Les membres ont le droit de participer aux activités organisées par l'association. Ils doivent respecter les statuts et le règlement intérieur de l'association.</p>
            <h4>Article 4 : Organes de l'association</h4>
            <p>L'association est dirigée par un conseil d'administration élu par les membres lors de l'assemblée générale annuelle.</p>
            <h4>Article 5 : Modifications du règlement intérieur</h4>
            <p>Le règlement intérieur peut être modifié lors de l'assemblée générale sur proposition du conseil d'administration.</p>
        ";
        //à recup

        $current_user = Auth::user();
        if(env("APP_DEBUG")){
            $adhesion_valid = true;
        }else{
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }
        // Retourner la vue avec le contenu du règlement intérieur
        return view('reglement_interieur', compact('reglement','adhesion_valid','current_user'));
    }
}
