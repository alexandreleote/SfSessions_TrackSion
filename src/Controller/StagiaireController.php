<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\SessionRepository;
use App\Repository\StagiaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class StagiaireController extends AbstractController{
    #[Route('/stagiaire', name: 'stagiaire_index')]
    #[IsGranted('ROLE_USER')]
    public function index(StagiaireRepository $sr,): Response
    {

        $stagiaires = $sr->findby([], ['nom' => 'ASC']);

        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaires,
        ]);
    }

    #[Route('/stagiaire/new', name: 'stagiaire_new')]
    #[Route('/stagiaire/{id}/edit', name: 'stagiaire_edit')]
    #[IsGranted('ROLE_PROFESSEUR')]
    public function new_edit(Stagiaire $stagiaire = null, Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$stagiaire) {
            $stagiaire = new Stagiaire();
        }

        $form = $this->createForm(StagiaireType::class, $stagiaire);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
        
            $stagiaire = $form->getData();

            $entityManager->persist($stagiaire);
            $entityManager->flush();

            return $this->redirectToRoute('stagiaire_show', ['id' => $stagiaire->getId()]);
        }
            
        return $this->render('stagiaire/new.html.twig', [
            'formNewStagiaire' => $form,
            'edit' => $stagiaire->getId(),
            
        ]);
    }

    #[Route('/stagiaire/{id}/delete', name: 'stagiaire_delete')]
    #[IsGranted('ROLE_PROFESSEUR')]
    public function delete(Stagiaire $stagiaire, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($stagiaire);
        $entityManager->flush();

        return $this->redirectToRoute('stagiaire_index');
    }

    #[Route('/stagiaire/add/{sessionId}', name: 'stagiaire_add')]
    #[IsGranted('ROLE_PROFESSEUR')]
    public function add(int $sessionId, EntityManagerInterface $entityManager, Request $request): Response
    {
        $session = $entityManager->getRepository(Session::class)->find($sessionId);
        
        if (!$session) {
            return $this->redirectToRoute('app_session');
        }

        // Fix: Use toArray() for array values
        $selectedStagiaires = $request->request->all('stagiaires');
        
        if (empty($selectedStagiaires)) {
            return $this->redirectToRoute('session_show', ['id' => $sessionId]);
        }

        if (count($selectedStagiaires) + $session->getNbPlacesReservees() > $session->getNbPlacesTotal()) {
            return $this->redirectToRoute('session_show', ['id' => $sessionId]);
        }

        foreach ($selectedStagiaires as $stagiaireId) {
            $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);
            if ($stagiaire) {
                $session->addStagiaire($stagiaire);
            }
        }

        $entityManager->flush();
        return $this->redirectToRoute('session_show', ['id' => $sessionId]);
    }

    #[Route('/stagiaire/remove/{sessionId}', name: 'stagiaire_remove')]
    #[IsGranted('ROLE_PROFESSEUR')]
    public function remove(int $sessionId, EntityManagerInterface $entityManager, Request $request): Response
    {
        $session = $entityManager->getRepository(Session::class)->find($sessionId);
        
        if (!$session) {
            return $this->redirectToRoute('session_index');
        }

        // Get single stagiaire ID from form
        $stagiaireId = $request->request->get('stagiaire_id');
        
        // Find and remove single stagiaire
        $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);
        if ($stagiaire) {
            $session->removeStagiaire($stagiaire);
            $entityManager->flush();
        }

        // Get the referer path
        $referer = $request->headers->get('referer');
        $path = parse_url($referer, PHP_URL_PATH);
        $firstSegment = explode('/', trim($path, '/'))[0];

        // Return based on source path
        return match($firstSegment) {
            'stagiaire' => $this->redirectToRoute('stagiaire_show', ['id' => $stagiaireId]),
            'session' => $this->redirectToRoute('session_show', ['id' => $sessionId]),
            default => $this->redirectToRoute('session_show', ['id' => $sessionId])
        };
    }

    #[Route('/stagiaire/{id}', name: 'stagiaire_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Stagiaire $stagiaire, SessionRepository $sr): Response
    {
        $currentSessions = $sr->findCurrentSessionsByStudent($stagiaire);
        $nextSessions = $sr->findNextSessionsByStudent($stagiaire);
        $pastSessions = $sr->findPastSessionsByStudent($stagiaire);

        return $this->render('stagiaire/show.html.twig', [
            'stagiaire' => $stagiaire,
            'currentSessions' => $currentSessions,
            'nextSessions' => $nextSessions,
            'pastSessions' => $pastSessions,
        ]);
    }
   
}