<?php

namespace App\Services;

use Smalot\Pdf\Parser;

class PdfParserService
{
    public function getText(string $filePath): string
    {
        $pdfParser = new \Smalot\PdfParser\Parser();
        $pdf = $pdfParser->parseFile($filePath);
        $page = $pdf->getPages()[0];
        $data = $page->getDataTm();

        $topRightTexts = array_filter($data, function ($item) {
            $x = $item[0][4]; // Position X
            $y = $item[0][5]; // Position Y
        
            return $x > 400 && $y > 700;
        });

        // Extraire uniquement le texte
        $texts = array_map(fn($item) => $item[1], $topRightTexts);
       
        // Retourner les textes sous forme de chaîne
        return implode( $texts);

        //return $pdf->getText();
    }
}