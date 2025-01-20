<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Session;
use App\Entity\Programme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CoursController extends AbstractController{
    #[Route('/cours', name: 'app_cours')]
    public function index(): Response
    {
        return $this->render('cours/index.html.twig', [
            'controller_name' => 'CoursController',
        ]);
    }
    
    #[Route('/cours/add', name: 'add_cours')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $coursId = $request->request->get('cours_id');
        $sessionId = $request->request->get('session_id');
        $duree = $request->request->get('duree');

        $cours = $entityManager->getRepository(Cours::class)->find($coursId);
        $session = $entityManager->getRepository(Session::class)->find($sessionId);

        if ($cours && $session) {
            $programme = new Programme();
            $programme->setCours($cours);
            $programme->setSession($session);
            $programme->setDuree($duree);

            $entityManager->persist($programme);
            $entityManager->flush();

            return $this->redirectToRoute('show_session', ['id' => $sessionId]);
        }

        return $this->redirectToRoute('show_session', ['id' => $sessionId]);
    }

    #[Route('/cours/remove', name:'remove_cours')]
    public function remove(Request $request, EntityManagerInterface $entityManager): Response
    {
        $programmeId = $request->request->get('programme_id');
        
        if (!$programmeId) {
            return $this->redirectToRoute('show_session');
        }

        $programme = $entityManager->getRepository(Programme::class)->find($programmeId);

        if ($programme) {
            $sessionId = $programme->getSession()->getId();
            $entityManager->remove($programme);
            $entityManager->flush();
            
            return $this->redirectToRoute('show_session', ['id' => $sessionId]);
        }

        return $this->redirectToRoute('show_session');
    }
}
