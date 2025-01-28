<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProfesseurController extends AbstractController
{
    #[Route('/professeur', name: 'professeur_index')]
    public function index(UserRepository $ur): Response
    {
        
        $users = $ur->findby([], ['nom' => 'ASC']);


        return $this->render('professeur/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/professeur/{id}', name: 'professeur_show')]
    public function show(User $user): Response
    {
        if (!in_array('ROLE_PROFESSEUR', $user->getRoles())) {
            $this->addFlash('warning', 'Cet utilisateur n\'est pas un formateur.');
            return $this->redirectToRoute('professeur_index');
        }
        
        return $this->render('professeur/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/professeur/setrole/{id}', name: 'professeur_setrole')]
    public function assignProfessorRole(User $user, EntityManagerInterface $em): Response 
    {
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        // Vérifier si l'utilisateur a déjà le rôle
        $roles = $user->getRoles();
        if (!in_array('ROLE_PROFESSEUR', $roles)) {
            $roles[] = 'ROLE_PROFESSEUR';
            $user->setRoles($roles);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "{$user->getIdentity()} a bien été .");
        } else {
            $this->addFlash('warning', "Cet utilisateur possède déjà le rôle 'Professeur'.");
        }

        return $this->redirectToRoute('professeur_index');
    }
}
