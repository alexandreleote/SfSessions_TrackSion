<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StagiaireController extends AbstractController{
    #[Route('/stagiaire', name: 'app_stagiaire')]
    public function index(): Response
    {
        return $this->render('stagiaire/index.html.twig', [
            'controller_name' => 'StagiaireController',
        ]);
    }

    #[Route('/stagiaire/create', name: 'app_stagiaire_create')]
    public function create(Stagiaire $stagiaire): Response
    {
        return $this->render('stagiaire/create.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }

    #[Route('/stagiaire/add', name: 'add_stagiaire')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stagiaireId = $request->request->get('stagiaire_id');
        $sessionId = $request->request->get('session_id');

        $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);
        $session = $entityManager->getRepository(Session::class)->find($sessionId);

        if ($stagiaire && $session) {
            $session->addStagiaire($stagiaire);
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('show_session', ['id' => $sessionId]);
        }

        return $this->redirectToRoute('show_session', ['id' => $sessionId]);
    }

    #[Route('/stagiaire/remove', name:'remove_stagiaire')]
    public function remove(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Debug pour voir les valeurs reçues
        $stagiaireId = $request->request->get('stagiaire_id');
        $sessionId = $request->request->get('session_id');
        
        // Vérification que les IDs ne sont pas null
        if (!$stagiaireId || !$sessionId) {
            return $this->redirectToRoute('show_session', ['id' => $sessionId]);
        }

        $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);
        $session = $entityManager->getRepository(Session::class)->find($sessionId);

        if ($stagiaire && $session) {
            $session->removeStagiaire($stagiaire);
            $entityManager->persist($session);
            $entityManager->flush();
            
        } 

        return $this->redirectToRoute('show_session', ['id' => $sessionId]);
    }
}