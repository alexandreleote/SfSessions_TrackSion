<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Service\DompdfService;
use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PdfController extends AbstractController
{
    #[Route('/pdf/generate/stagiaire/{id}', name: 'generate_pdf')]
    public function generatePdf(Stagiaire $stagiaire, DompdfService $dompdfService, SessionRepository $sr): Response
    {

        $title = "Informations concernant : ".$stagiaire;

        $currentSessions = $sr->findByCurrentSessions();
        $nextSessions = $sr->findByNextSessions();
        $pastSessions = $sr->findByPastSessions();

        $html = $this->renderView('pdf/template.html.twig', [
            'title' => $title,
            'stagiaire' => $stagiaire,
            'currentSessions' =>$currentSessions,
            'nextSessions' =>$nextSessions,
            'pastSessions' =>$pastSessions,
        ]);

        $dompdfService->showPdfFile($html);

        return new Response('', 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }
}
 