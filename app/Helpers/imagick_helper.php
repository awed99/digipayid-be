<?php
set_time_limit(300);

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




        // define("DOMPDF_ENABLE_HTML5PARSER", true);
        // define("DOMPDF_ENABLE_FONTSUBSETTING", true);
        // define("DOMPDF_UNICODE_ENABLED", true);
        // define("DOMPDF_DPI", 350);
        // define("DOMPDF_ENABLE_REMOTE", true);

        // $pdf = new Dompdf();
        // $options = $pdf->getOptions();
        // $pdf->setPaper(array(0, 0, 700, 800), 'portrait');

        // $options->set(array(
        //     'isRemoteEnabled' => true,
        //     'isHtml5ParserEnabled' => true
        // ));
        // $pdf->setOptions($options);
        // $pdf->loadHtml($html);

        // /*
        // * Workaround to get the body height
        // */
        // $GLOBALS['bodyHeight'] = 0;
        // $pdf->setCallbacks([
        //     'myCallbacks' => [
        //         'event' => 'end_frame',
        //         'f' => function ($frame) {
        //             $node = $frame->get_node();

        //             if (strtolower($node->nodeName) === "body") {
        //                 $padding_box = $frame->get_padding_box();
        //                 $GLOBALS['bodyHeight'] += $padding_box['h'];
        //             }
        //         }
        //     ]
        // ]);
        // $pdf->render();
        // unset($pdf);
        // $docHeight = $GLOBALS['bodyHeight'];



        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $dompdf->setPaper(array(0, 0, 700, 3000), 'portrait');
        // $dompdf->setPaper(array(0, 0, 700, $docHeight), 'portrait');
        // $dompdf->setPaper(array(0, 0, 595, 841), 'portrait');

        $options->set(array(
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            // 'dpi' => 2400,
        ));
        // $dompdf->setDpi(350);
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
        $imagick->setResolution(2100, 9000);
        // $imagick->resizeImage(2100, 9000, \Imagick::FILTER_LANCZOS, 1, false);
        // unlink($urlHTML);

        $range = $imagick->getQuantumRange();
        $imagick->trimImage(0 * $range['quantumRangeLong']);
        // $imagick->trimImage(0 * \Imagick::getQuantum());

        
        $imagick->writeImage($urlIMG);


        if (file_exists($urlHTML)) {
            // unlink($urlHTML);
        }

        if (file_exists($urlPDF)) {
            // unlink($urlPDF);
        }

        // if (file_exists($urlIMG)) {
        //     unlink($urlIMG);
        // }

        return getDomain() . '/' . $urlIMG;
    }
}
