<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SessionRepository;

final class HomeController extends AbstractController{

    #[Route('/home', name: 'app_home')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $currentSessions = $sessionRepository->findByCurrentSessions();
        $nextSessions = $sessionRepository->findByNextSessions();
        $pastSessions = $sessionRepository->findByPastSessions();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'SessionController',
            'currentSessions' => $currentSessions,
            'nextSessions' => $nextSessions,
            'pastSessions' => $pastSessions,
        ]);
    }
}