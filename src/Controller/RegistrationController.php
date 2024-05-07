<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(# Cette méthode prend en charge le hachage du mot de passe. Elle prend deux arguments .
                $userPasswordHasher->hashPassword(
                    $user,#C'est l'entité utilisateur à laquelle le mot de passe est associé.
                    $form->get('plainPassword')->getData()  #$form->get('plainPassword')->getData(): C'est le mot de passe brut récupéré à partir du formulaire.
                )
            );

            $user->setRoles([$form->get('role')->getData()]);#insérant un nouvel enregistrement avec les rôles spécifiés dans la table des utilisateurs.
            $entityManager->persist($user);#Cette ligne utilise l'EntityManager de Doctrine pour marquer l'entité utilisateur $user comme étant à persister
            $entityManager->flush();#déclenche l'exécution réelle de toutes les opérations d'enregistrement (persist, update, delete) qui ont été enregistrées avec l'EntityManager

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, # C'est un appel à la méthode sendEmailConfirmation() de l'objet $this->emailVerifier. Cette méthode est responsable de l'envoi de l'e-mail de confirmation
                (new TemplatedEmail())#Cela crée une nouvelle instance de la classe TemplatedEmail
                    ->from(new Address('ines.rouatbi14@gmail.com', 'ines.rouatbi14@gmail.com'))#l'adresse e-mail de l'expéditeur
                    ->to($user->getEmail())#l'adresse e-mail du destinataire
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [#  génère une réponse HTTP sous forme de page HTML  C'est le chemin vers le modèle Twig à utiliser pour générer la page HTML
            'registrationForm' => $form->createView(),#C'est un tableau associatif contenant les données à passer au modèle Twig pour son rendu
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {#levée lorsqu'il y a un problème avec la vérification de l'e-mail.
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_home');
    }
}
