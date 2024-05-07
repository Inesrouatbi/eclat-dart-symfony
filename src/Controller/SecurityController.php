<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
class SecurityController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();#Cette ligne de code utilise le service AuthenticationUtils pour récupérer la dernière erreur d'authentification
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();//utilise également le service AuthenticationUtils pour récupérer le dernier nom d'utilisateur saisi.

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);//Elle transmet également les variables $lastUsername et $error à la vue Twig pour les afficher.
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
