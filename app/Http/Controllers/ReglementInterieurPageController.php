<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReglementInterieurPageController extends Controller
{
    /**
     * Show the internal regulation.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $current_user = Auth::user();
        if(env("APP_DEBUG")){
            $adhesion_valid = true;
        }else{
            $adhesion_valid = $current_user ? $current_user->hasUpToDateAdhesion() : false;
        }

        $pdfExists = Storage::disk('public')->exists('documents/reglement_interieur.pdf');

        return view('reglement_interieur', compact('adhesion_valid', 'current_user', 'pdfExists'));
    }

    /**
     * Download the internal regulation in PDF.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download()
    {
        $path = 'documents/reglement_interieur.pdf';

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Le fichier PDF du règlement intérieur n\'a pas été trouvé.');
        }

        return Storage::disk('public')->download($path, 'ADOLICES Règlement Intérieur.pdf');
    }
}
