<?php

use Dompdf\Dompdf;

function htmlToImage($html, $invoice_number, $url)
{
    if (extension_loaded('imagick')) {

        $urlHTML = $url . ".html";
        $urlPDF = $url . ".pdf";
        $urlIMG = $url . ".png";

        if (file_exists($urlHTML)) {
            unlink($urlHTML);
        }
        $fp = fopen($urlHTML, 'x');
        fwrite($fp, $html);
        fclose($fp);


        $fpx = fopen($urlHTML, 'r');
        $contents = fread($fpx, filesize($urlHTML));
        fclose($fpx);

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $dompdf->setPaper(array(0, 0, 500, 1000), 'portrait');

        $options->set(array(
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true
        ));
        $dompdf->setOptions($options);
        $dompdf->loadHtml($contents);

        // (Optional) Setup the paper size and orientation
        // $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();


        $output = $dompdf->output();
        file_put_contents($urlPDF, $output);

        // Output the generated PDF to Browser
        // $dompdf->stream();

        // if (file_exists($urlHTML)) {
        //     unlink($urlHTML);
        // }
        // $fp = fopen($urlHTML, 'x');
        // fwrite($fp, $html);
        // fclose($fp);

        $imagick = new imagick(realpath($urlPDF));
        $imagick->setImageFormat('png');
        $imagick->writeImage($urlIMG);
        // unlink($urlHTML);


        if (file_exists($urlHTML)) {
            unlink($urlHTML);
        }

        if (file_exists($urlPDF)) {
            unlink($urlPDF);
        }

        // if (file_exists($urlIMG)) {
        //     unlink($urlIMG);
        // }

        return getenv('API_DOMAIN_BASE_URL') . $urlIMG;
    }
}
