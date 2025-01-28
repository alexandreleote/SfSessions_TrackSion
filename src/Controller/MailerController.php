<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer')]
    public function index(MailerInterface $mailer): Response
    {
        // Construire l'e-mail
        $email = (new Email())
            ->from('test@example.com') // Adresse d'expéditeur
            ->to('recipient@example.com') // Adresse du destinataire
            ->subject('Test Email from Symfony')
            ->text('Ceci est un e-mail envoyé depuis Symfony.')
            ->html('<p>Ceci est un e-mail envoyé depuis Symfony.</p>');

        // Envoyer l'e-mail
        try {
            $mailer->send($email);
            $message = 'L\'e-mail a été envoyé avec succès !';
        } catch (\Exception $e) {
            $message = 'Une erreur est survenue : ' . $e->getMessage();
        }

        // Retourner la réponse
        return $this->render('mailer/index.html.twig', [
            'controller_name' => 'MailerController',
            'message' => $message,
        ]);
    }
}
