<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = new User();
        
        $form = $this->createForm(RegisterType::class);

        $form->handleRequest($request, $user);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été créé');
            
            // Envoi d'un email de bienvenue
            $mail = new Mail();
            $vars= [
                'firstname' => $user->getFirstname()
            ];
            $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Bienvenue sur la boutique ASSO', 'welcome.html', $vars);
    

            return $this->redirectToRoute('app_login');
        }


        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
