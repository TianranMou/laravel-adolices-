<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PdfParserService;

class PdfController extends Controller
{
    /**
     * @var PdfParserService
     * Service utilisé pour extraire le texte des fichiers PDF.
     */
    protected $pdfParserService;

    /**
     * Constructeur du contrôleur.
     * 
     * @param PdfParserService $pdfParserService
     * Injecte le service d'analyse de PDF pour pouvoir l'utiliser dans les méthodes du contrôleur.
     */
    public function __construct(PdfParserService $pdfParserService)
    {
        $this->pdfParserService = $pdfParserService;
    }

    /**
     * Analyse un fichier PDF et extrait son texte.
     * 
     * @return \Illuminate\Http\JsonResponse
     * Retourne le texte extrait du fichier PDF sous forme de réponse JSON.
     */
    public function parsePdf()
    {
        $filePath = public_path('Majestic.pdf'); // le bon chemin ici
        $text = $this->pdfParserService->getText($filePath);

        return response()->json(['text' => $text]);
    }
}
