<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Session;
use App\Form\CoursType;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Form\ProgrammeType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class SessionController extends AbstractController{
    #[Route('/session', name: 'session_index')]
    #[IsGranted('ROLE_USER')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $currentSessions = $sessionRepository->findByCurrentSessions();
        $nextSessions = $sessionRepository->findByNextSessions();
        $pastSessions = $sessionRepository->findByPastSessions();

        return $this->render('session/index.html.twig', [
            'currentSessions' => $currentSessions,
            'nextSessions' => $nextSessions,
            'pastSessions' => $pastSessions,
        ]);
    }

    #[Route('/session/new', name: 'session_new')]
    #[Route('/session/{id}/edit', name: 'session_edit')]
    #[IsGranted('ROLE_PROFESSEUR')]
    public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response
    {

        if (!$session) {
            $session = new Session();
        }

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
        
            $session = $form->getData();

            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('session_show', ['id' => $session->getId()]);
        }
            
        return $this->render('session/new.html.twig', [
            'formNewSession' => $form,
            'edit' => $session->getId(),
            
        ]);
    }

    #[Route('/session/{id}/delete', name: 'session_delete')]
    #[IsGranted('ROLE_PROFESSEUR')]
    public function delete(Session $session, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($session);
        $entityManager->flush();

        return $this->redirectToRoute('app_session');
    }

    #[Route('/session/{session}/cours/{cours}/add', name: 'cours_add')]
    #[IsGranted('ROLE_PROFESSEUR')]
    public function addCours(Session $session, Cours $cours, Request $request, EntityManagerInterface $entityManager): Response
    {
        $duree = $request->request->get('duree');
        
        // Create new Programme
        $programme = new Programme();
        $programme->setCours($cours);
        $programme->setSession($session);
        $programme->setDuree($duree);
        
        $entityManager->persist($programme);
        $entityManager->flush();
        
        return $this->redirectToRoute('session_show', ['id' => $session->getId()]);
    }

    #[Route('/session/{session}/cours/{cours}/remove', name: 'cours_remove')]
    #[IsGranted('ROLE_PROFESSEUR')]
    public function removeCours(Session $session, Cours $cours, EntityManagerInterface $entityManager): Response
    {
        $programme = $entityManager->getRepository(Programme::class)->findOneBy([
            'session' => $session,
            'cours' => $cours
        ]);
        
        if ($programme) {
            $entityManager->remove($programme);
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_show', ['id' => $session->getId()]);
    }

    #[Route('/session/{id}', name: 'session_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Session $session, SessionRepository $sessionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {

        // Récupérer les stagiaires de la session et les trier
        $stagiaires = $session->getStagiaires()->toArray();
        usort($stagiaires, function($a, $b) {
            return strcmp($a->getNom(), $b->getNom());
        });

        $nonInscrits = $sessionRepository->findNonInscrits($session->getId());
        $nonProgrammes = $sessionRepository->findNonProgrammes($session);
    
        $form = $this->createForm(ProgrammeType::class, null, [
            'nonProgrammes' => $nonProgrammes
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $programmes = $form->getData()['programmes'];
            foreach ($programmes as $programme) {
                $programme->setSession($session);
                $entityManager->persist($programme);
            }
            $entityManager->flush();
            
            return $this->redirectToRoute('session_show', ['id' => $session->getId()]);
        }

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'stagiaires' => $stagiaires,
            'formProgramme' => $form->createView(),
            'nonProgrammes' => $nonProgrammes,
            'nonInscrits' => $nonInscrits,
        ]);
    }
    
}
