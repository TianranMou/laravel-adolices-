<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PdfParserService;

class PdfController extends Controller
{
    protected $pdfParserService;

    public function __construct(PdfParserService $pdfParserService)
    {
        $this->pdfParserService = $pdfParserService;
    }

    public function parsePdf()
    {
        $filePath = public_path('Majestic.pdf'); // Mets le bon chemin ici
        $text = $this->pdfParserService->getText($filePath);

        return response()->json(['text' => $text]);
    }
}
