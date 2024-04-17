<?php

namespace App\Controller;

use App\Entity\Oeuvres;
use App\Form\OeuvresType;
use App\Repository\OeuvresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/oeuvres')]
class OeuvresController extends AbstractController
{
    #[Route('/', name: 'app_oeuvres_index', methods: ['GET'])]
    public function index(OeuvresRepository $oeuvresRepository): Response
    {
        $categories = ['Peinture', 'Sculpture', 'Gravure','Céramique'];

        return $this->render('oeuvres/index.html.twig', [
            'oeuvres' => $oeuvresRepository->findAll(),
            'categories' => $categories,
        ]);
    }
    #[Route('/front', name: 'app_oeuvres_indexFront', methods: ['GET'])]
    public function indexFront(OeuvresRepository $oeuvresRepository): Response
    {
        $categories = ['Peinture', 'Sculpture', 'Gravure','Céramique'];

        return $this->render('oeuvres/indexFront.html.twig', [
            'oeuvres' => $oeuvresRepository->findAll(),
            'categories' => $categories,
        ]);
    }
    #[Route('/load-oeuvres', name: 'load_oeuvres')]
    // In your loadOeuvres controller method
    public function loadOeuvres(Request $request, OeuvresRepository $oeuvresRepository): Response
    {
        $categorie = $request->query->get('categorie');
        $titre = $request->query->get('titre');
        $categories = ['Peinture', 'Sculpture', 'Gravure', 'Céramique'];

        $filteredOeuvres = $oeuvresRepository->findByFilters($categorie, $titre);

        if ($request->isXmlHttpRequest()) {
            // If it's an AJAX request, only return the part of the template that generates the table rows
            return $this->render('oeuvres/partials/_oeuvresTable.html.twig', [
                'oeuvres' => $filteredOeuvres,
            ]);
        } else {
            // If it's not an AJAX request, render the entire page
            return $this->render('oeuvres/index.html.twig', [
                'categories' => $categories,
                'oeuvres' => $filteredOeuvres,
            ]);
        }
    }
    #[Route('/load-oeuvresFront', name: 'load_oeuvresFront')]
    // In your loadOeuvres controller method
    public function loadOeuvresFront(Request $request, OeuvresRepository $oeuvresRepository): Response
    {
        $categorie = $request->query->get('categorie');
        $titre = $request->query->get('titre');
        $categories = ['Peinture', 'Sculpture', 'Gravure', 'Céramique'];

        $filteredOeuvres = $oeuvresRepository->findByFilters($categorie, $titre);

        if ($request->isXmlHttpRequest()) {
            // If it's an AJAX request, only return the part of the template that generates the table rows
            return $this->render('oeuvres/partials/_oeuvresGrid.html.twig', [
                'oeuvres' => $filteredOeuvres,
            ]);
        } else {
            // If it's not an AJAX request, render the entire page
            return $this->render('oeuvres/indexFront.html.twig', [
                'categories' => $categories,
                'oeuvres' => $filteredOeuvres,
            ]);
        }
    }

    #[Route('/new', name: 'app_oeuvres_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager , SluggerInterface $slugger): Response
    {
        $oeuvre = new Oeuvres();
        $form = $this->createForm(OeuvresType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('img')->getData();
            $oeuvre->setIduser(1) ;

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $oeuvre->setImg($newFilename);
            }
            $entityManager->persist($oeuvre);
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('oeuvres/new.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_oeuvres_show', methods: ['GET'])]
    public function show(Oeuvres $oeuvre): Response
    {
        return $this->render('oeuvres/show.html.twig', [
            'oeuvre' => $oeuvre,
        ]);
    }
    #[Route('/{id}', name: 'app_oeuvres_showFront', methods: ['GET'])]
    public function showFront(Oeuvres $oeuvre): Response
    {
        return $this->render('oeuvres/showFront.html.twig', [
            'oeuvre' => $oeuvre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_oeuvres_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Oeuvres $oeuvre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OeuvresType::class, $oeuvre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oeuvre->setIduser(1) ;
            $entityManager->flush();

            return $this->redirectToRoute('app_oeuvres_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('oeuvres/edit.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_oeuvres_delete', methods: ['POST'])]
    public function delete(Request $request, Oeuvres $oeuvre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oeuvre->getIdoeuvre(), $request->request->get('_token'))) {
            $entityManager->remove($oeuvre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_oeuvres_index', [], Response::HTTP_SEE_OTHER);
    }
}
