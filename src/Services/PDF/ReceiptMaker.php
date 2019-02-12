<?php

namespace App\Services\PDF;


use App\Entity\Receipt;

class ReceiptMaker
{
    private $twig;

    function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    function make(Receipt $receipt){
        $htmlTable = $this->twig->render('receipt/pdf.html.twig', ['receipt' => $receipt]);


        $filename = 'receipts/'.md5(microtime()).'.pdf';
        $pdf->Output($filename, 'F');
        return $filename;
    }
}