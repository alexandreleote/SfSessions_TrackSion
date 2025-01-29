<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ProfesseurController extends AbstractController
{
    #[Route('/formateur', name: 'formateur_index')]
    #[IsGranted('ROLE_USER')]  
    public function index(UserRepository $ur): Response
    {
        
        $users = $ur->findby([], ['nom' => 'ASC']);


        return $this->render('formateur/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/formateur/{id}', name: 'formateur_show')]
    #[IsGranted('ROLE_USER')]
    public function show(User $user): Response
    {
        /* if (!in_array('ROLE_PROFESSEUR', $user->getRoles())) {
            $this->addFlash('warning', 'Cet utilisateur n\'est pas un formateur.');
            return $this->redirectToRoute('formateur_index');
        } */
        
        return $this->render('formateur/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/formateur/{id}/edit', name: 'formateur_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(User $user = null, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $currentUser = $security->getUser();

        // Vérifier si l'utilisateur à modifier existe
        if (!$user) {
            return $this->redirectToRoute('formateur_index');
        }

        // Vérifier si l'utilisateur est connecté
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur connecté est soit l'admin soit le propriétaire du profil
        if (!$this->isGranted('ROLE_ADMIN') && $currentUser->getId() !== $user->getId()) {
            return $this->redirectToRoute('formateur_index');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('formateur_show', ['id' => $user->getId()]);
        }
            
        return $this->render('formateur/edit.html.twig', [
            'formEditUser' => $form,
            'edit' => $user->getId(),
            'user' => $user,
        ]);
    }

    #[Route('/formateur/{id}/delete', name: 'formateur_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('formateur_index');
    }

    #[Route('/formateur/setrole/{id}', name: 'formateur_setrole')]
    #[IsGranted('ROLE_ADMIN')]
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

        return $this->redirectToRoute('formateur_index');
    }
}
