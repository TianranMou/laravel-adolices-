<?php

namespace App\Services;

use Smalot\Pdf\Parser;

class PdfParserService
{
    public function getText(string $filePath): string
    {

        //Ajuster la position du parser de PDF 

        $pdfParser = new \Smalot\PdfParser\Parser();
        $pdf = $pdfParser->parseFile($filePath);
        $page = $pdf->getPages()[0]; // récupère uniquement la première page du PDF
        $data = $page->getDataTm();

        $topRightTexts = array_filter($data, function ($item) {
            $x = $item[0][4]; // Position X
            $y = $item[0][5]; // Position Y
        
            return $x > 400 && $y > 700; //position à déterminer
        });

        $validityDate = array_filter($data,function ($item){
            $x = $item[0][4]; // Position X
            $y = $item[0][5]; // Position Y

            return $x < 400 && $y > 780;
        });

        // Extraire Partner Code et partner ID
        $texts = array_map(fn($item) => $item[1], $topRightTexts);
        $separator = explode('Clé Web :', implode($texts));

        //Extraire la date de validité
        $date = array_map(fn($item) => $item[1], $validityDate );

        $validity = explode("Valable jusqu'au: ", implode($date))[1];
        $validity = preg_replace('/^\s+/', '', $validity); // Supprime les espaces au début
        $partnerId = $separator[0];
        $partnerCode = $separator[1];

        if (count($separator) < 2) {
            return ['partner_id' => null, 'partner_code' => null];
        }

        /*
        return [
            'partner_id' => trim($separator[0]),
            'partner_code' => trim($separator[1])
        ];
        */

        // Retourner lespartner code et partner id sous forme de chaîne
        return " $validity|$partnerId|$partnerCode" ;

        //return $pdf->getText();
    }
}