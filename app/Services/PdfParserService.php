<?php

namespace App\Services;

use Smalot\Pdf\Parser;

class PdfParserService
{
    /**
     * Analyse un fichier PDF spécifique (Majestic) et extrait des informations clés.
     *
     * @param string $filePath Chemin du fichier PDF à analyser.
     * @return string Retourne une chaîne contenant la date de validité, l'ID partenaire et le code partenaire, séparés par "|".
     */
    public function getTextMajestic(string $filePath): string
    {

        //Ajuster la position du parser de PDF 

        $pdfParser = new \Smalot\PdfParser\Parser();
        $pdf = $pdfParser->parseFile($filePath);
        $page = $pdf->getPages()[0]; // récupère uniquement la première page du PDF
        $data = $page->getDataTm();

        /**
         * Extraction des informations situées en haut à droite du document (Partner ID et Partner Code).
         * On filtre les données pour ne conserver que celles ayant une position X > 400 et Y > 700 ce qui est spécifique au document Majestic.
         */
        $topRightTexts = array_filter($data, function ($item) {
            $x = $item[0][4]; 
            $y = $item[0][5]; 
        
            return $x > 400 && $y > 700; //position à déterminer
        });

        /**
         * Extraction de la date de validité du ticket.
         * On filtre les données ayant une position X < 400 et Y > 780 (supposée être la position de la date).
         */
        $validityDate = array_filter($data,function ($item){
            $x = $item[0][4]; 
            $y = $item[0][5]; 

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

        // Retourner lespartner code et partner id sous forme de chaîne
        return " $validity|$partnerId|$partnerCode" ;

    }
}