<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class DompdfService
{
    private $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $this->dompdf = new Dompdf($options);
    }

    public function showPdfFile($html)
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        $this->dompdf->stream("document.pdf", [
            "Attachment" => false
        ]);
    }

    public function generatePdfFile($html, $filename)
    {
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        file_put_contents($filename, $this->dompdf->output());
    }
}