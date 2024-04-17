<?php

namespace App\Controller;

use App\Entity\Oeuvres;
use App\Entity\Reservations;
use App\Form\ReservationsType;
use App\Repository\OeuvresRepository;
use App\Repository\ReservationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservations')]
class ReservationsController extends AbstractController
{
    #[Route('/', name: 'app_reservations_index', methods: ['GET'])]
    public function index(ReservationsRepository $reservationsRepository): Response
    {
        return $this->render('reservations/index.html.twig', [
            'reservations' => $reservationsRepository->findAll(),
        ]);
    }
    #[Route('/oeuvre/{id}/reserve', name: 'app_oeuvres_reserve', methods: ['GET', 'POST'])]
    public function reserve(Request $request, OeuvresRepository $oeuvresRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $oeuvre = $oeuvresRepository->find($id);

        if (!$oeuvre) {
            throw $this->createNotFoundException('No oeuvre found for id '.$id);
        }

        $reservation = new Reservations();

        $form = $this->createForm(ReservationsType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setOeuvreID($oeuvre);
            $reservation->setStatut('Pending') ;
            $entityManager->persist($reservation);
            $entityManager->flush();
            return $this->redirectToRoute('app_reservations_show', ['idReservation' => $reservation->getIdReservation()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservations/new.html.twig', [
            'oeuvre' => $oeuvre,
            'form' => $form->createView(),
            'is_new' => true,
        ]);
    }
    #[Route('/new', name: 'app_reservations_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservations();
        $form = $this->createForm(ReservationsType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservations/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{idReservation}', name: 'app_reservations_show', methods: ['GET'])]
    public function show(Reservations $reservation): Response
    {
        return $this->render('reservations/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
    #[Route('/{idReservation}/back', name: 'app_reservations_showBack', methods: ['GET'])]
    public function showBack(Reservations $reservation): Response
    {
        return $this->render('reservations/showBack.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{idReservation}/edit', name: 'app_reservations_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservations $reservation, EntityManagerInterface $entityManager): Response
    {
        // Pass the current oeuvre ID to the form
        $currentOeuvreId = $reservation->getOeuvreID()?->getIdoeuvre();
        $form = $this->createForm(ReservationsType::class, $reservation, [
            'oeuvreID' => $currentOeuvreId
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Since 'oeuvreID' is not mapped, manually set it
            $oeuvreID = $form->get('oeuvreID')->getData();
            if ($oeuvreID) {
                $oeuvre = $entityManager->getRepository(Oeuvres::class)->find($oeuvreID);
                if ($oeuvre) {
                    $reservation->setOeuvreID($oeuvre);
                } else {
                    // If oeuvre is not found, you might want to handle this case
                    throw new TransformationFailedException(sprintf(
                        'An oeuvre with ID "%s" does not exist!',
                        $oeuvreID
                    ));
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_reservations_index');
        }

        return $this->renderForm('reservations/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }



    #[Route('/{idReservation}', name: 'app_reservations_delete', methods: ['POST'])]
    public function delete(Request $request, Reservations $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getIdReservation(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservations_index', [], Response::HTTP_SEE_OTHER);
    }
}
