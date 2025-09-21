<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;

class PdfRenderer
{
    private string $publicDir;

    public function __construct(ParameterBagInterface $params)
    {
        // Kernel project dir is one level up from public
        $this->publicDir = rtrim($params->get('kernel.project_dir'), '/') . '/public';
    }

    public function renderPdfResponse(string $html, string $filename = 'report.pdf'): Response
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        // Restrict Dompdf to our public directory for assets
        $options->setChroot($this->publicDir);

        $dompdf = new Dompdf($options);
        $dompdf->setBasePath($this->publicDir);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfOutput = $dompdf->output();

        return new Response(
            $pdfOutput,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]
        );
    }
}