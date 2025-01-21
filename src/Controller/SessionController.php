<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Programme;
use App\Form\SessionType;
use App\Form\ProgrammeType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SessionController extends AbstractController{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $currentSessions = $sessionRepository->findByCurrentSessions();
        $nextSessions = $sessionRepository->findByNextSessions();
        $pastSessions = $sessionRepository->findByPastSessions();

        return $this->render('session/index.html.twig', [
            'controller_name' => 'SessionController',
            'currentSessions' => $currentSessions,
            'nextSessions' => $nextSessions,
            'pastSessions' => $pastSessions,
        ]);
    }

    #[Route('/session/add', name: 'add_session')]
    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function add(EntityManagerInterface $entityManager, Session $session = null, Request $request, Programme $programme): Response
    {
        if(!$session) {
            $session = new Session();
        }

        $form = $this->createForm(SessionType::class, $session);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
        
            $session = $form->getData();

            $entityManager->persist($session);
        
            $entityManager->flush();

            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        }

        return $this->render('session/add.html.twig', [
            'session' => $session,
            'formAddSession' => $form,
            'edit' => $session->getId(),
        ]);
    }

    #[Route('/session/{id}', name: 'show_session')]
    public function show(Session $session = null, SessionRepository $sr, EntityManagerInterface $em, Programme $programme): Response
    {

        if (!$session) {
            return $this->redirectToRoute('app_session');
        }

        $nonInscrits = $sr->findNonInscrits($session->getId());
        $nonProgrammes = $sr->findNonProgrammes($session->getId());

        /* $form = $this->createForm(ProgrammeType::class, $programme);

        $form->handlerequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $programme = $form->getData();
            $em->persist($programme);
            $em->flush();

            return $this->redirectToRoute('show_session', ['id' => $session->getId()]);
        } */

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'nonInscrits' => $nonInscrits,
            'nonProgrammes' => $nonProgrammes,
            /* 'formAddProgramme' => $form, */
        ]);
    }


}
