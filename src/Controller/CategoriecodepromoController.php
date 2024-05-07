<?php

namespace App\Controller;

use App\Entity\Categoriecodepromo;
use App\Form\CategoriecodepromoType;
use App\Repository\CategoriecodepromoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categoriecodepromo')]
class CategoriecodepromoController extends AbstractController
{
    #[Route('/', name: 'app_categoriecodepromo_index', methods: ['GET'])]
    public function index(CategoriecodepromoRepository $categoriecodepromoRepository): Response
    {
        return $this->render('categoriecodepromo/index.html.twig', [#cette ligne de code génère une réponse HTTP sous forme de page HTML en utilisant le modèle Twig categoriecodepromo/index.html.twig
            'categoriecodepromos' => $categoriecodepromoRepository->findAll(),#responsable de récupérer toutes les instances de l'entité Categoriecodepromo à partir de la base de données à l'aide du repository CategoriecodepromoRepository et de les transmettre au modèle de vue pour être affichées.
        ]);
    }

    #[Route('/new', name: 'app_categoriecodepromo_new', methods: ['GET', 'POST'])] #new: Affiche le formulaire pour créer une nouvelle catégorie de code promo et traite la 
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoriecodepromo = new Categoriecodepromo();#crée une nouvelle instance de l'entité Categoriecodepromo
        $form = $this->createForm(CategoriecodepromoType::class, $categoriecodepromo);#Cette ligne crée un formulaire associé à l'entité
        $form->handleRequest($request);# Cette méthode traite la demande HTTP actuelle ($request) pour le formulaire

        if ($form->isSubmitted() && $form->isValid()) {# Cette ligne vérifie si le formulaire a été soumis et si les données saisies sont valides.et is valide  Vérifie si les données saisies dans le formulaire sont valides
            $entityManager->persist($categoriecodepromo);#Cette ligne indique à l'EntityManager de suivre l'entité $categoriecodepromo et de la préparer à être persistée en base de données lors du prochain appel à flush()
            $entityManager->flush();

            return $this->redirectToRoute('app_categoriecodepromo_index', [], Response::HTTP_SEE_OTHER);#Elle génère une redirection HTTP vers une route spécifique de l'application Response::HTTP_SEE_OTHER: C'est le code de statut HTTP qui sera utilisé pour la redirection. HTTP_SEE_OTHER est une constante de la classe Response qui correspond au code de statut 303, indiquant que la réponse à la requête peut être trouvée sous une autre URL.
        }

        return $this->renderForm('categoriecodepromo/new.html.twig', [#Cette ligne génère une redirection HTTP vers une route spécifique 
            'categoriecodepromo' => $categoriecodepromo,#ne instance de l'entité Categoriecodepromo
            'form' => $form,#$form représente l'objet formulaire créé à l'aide de Symfony Forms. form'. Cela permet au modèle Twig d'afficher le formulaire dans la page HTML
        ]);
    }

    #[Route('/{id}', name: 'app_categoriecodepromo_show', methods: ['GET'])]
    public function show(Categoriecodepromo $categoriecodepromo): Response
    {
        return $this->render('categoriecodepromo/show.html.twig', [
            'categoriecodepromo' => $categoriecodepromo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categoriecodepromo_edit', methods: ['GET', 'POST'])]# Affiche le formulaire pour modifier une catégorie de code promo existante et traite la soumission du formulaire.
    public function edit(Request $request, Categoriecodepromo $categoriecodepromo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriecodepromoType::class, $categoriecodepromo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categoriecodepromo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categoriecodepromo/edit.html.twig', [
            'categoriecodepromo' => $categoriecodepromo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categoriecodepromo_delete', methods: ['POST'])]
    public function delete(Request $request, Categoriecodepromo $categoriecodepromo, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categoriecodepromo->getId(), $request->request->get('_token'))) {#C'est une méthode fournie par Symfony pour vérifier la validité d'un jeton CSRF
            $entityManager->remove($categoriecodepromo);# $categoriecodepromo->getId() C'est la valeur utilisée pour générer le jeton CSRF
            $entityManager->flush(); # L'EntityManager est responsable de la gestion des entités et de leur persistance dans la base de données.flush C'est une méthode de l'EntityManager qui synchronise toutes les opérations d'insertion, de mise à jour et de suppression des entités avec la base de données. #
        }

        return $this->redirectToRoute('app_categoriecodepromo_index', [], Response::HTTP_SEE_OTHER);
    }
}
