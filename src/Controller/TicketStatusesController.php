<?php

namespace App\Controller;

use App\Entity\TicketStatuses;
use App\Form\TicketStatusesType;
use App\Repository\TicketStatusesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ticket/statuses')]
class TicketStatusesController extends AbstractController
{
    #[Route('/', name: 'app_ticket_statuses_index', methods: ['GET'])]
    public function index(TicketStatusesRepository $ticketStatusesRepository): Response
    {
        return $this->render('ticket_statuses/index.html.twig', [
            'ticket_statuses' => $ticketStatusesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ticket_statuses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticketStatus = new TicketStatuses();
        $form = $this->createForm(TicketStatusesType::class, $ticketStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticketStatus);
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_statuses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket_statuses/new.html.twig', [
            'ticket_status' => $ticketStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_statuses_show', methods: ['GET'])]
    public function show(TicketStatuses $ticketStatus): Response
    {
        return $this->render('ticket_statuses/show.html.twig', [
            'ticket_status' => $ticketStatus,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ticket_statuses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TicketStatuses $ticketStatus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketStatusesType::class, $ticketStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_statuses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket_statuses/edit.html.twig', [
            'ticket_status' => $ticketStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_statuses_delete', methods: ['POST'])]
    public function delete(Request $request, TicketStatuses $ticketStatus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticketStatus->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ticketStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_statuses_index', [], Response::HTTP_SEE_OTHER);
    }
}
