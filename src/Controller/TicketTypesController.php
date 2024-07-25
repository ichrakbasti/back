<?php

namespace App\Controller;

use App\Entity\TicketTypes;
use App\Form\TicketTypesType;
use App\Repository\TicketTypesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ticket/types')]
class TicketTypesController extends AbstractController
{
    #[Route('/', name: 'app_ticket_types_index', methods: ['GET'])]
    public function index(TicketTypesRepository $ticketTypesRepository): Response
    {
        return $this->render('ticket_types/index.html.twig', [
            'ticket_types' => $ticketTypesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ticket_types_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticketType = new TicketTypes();
        $form = $this->createForm(TicketTypesType::class, $ticketType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticketType);
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_types_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket_types/new.html.twig', [
            'ticket_type' => $ticketType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_types_show', methods: ['GET'])]
    public function show(TicketTypes $ticketType): Response
    {
        return $this->render('ticket_types/show.html.twig', [
            'ticket_type' => $ticketType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ticket_types_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TicketTypes $ticketType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketTypesType::class, $ticketType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ticket_types_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ticket_types/edit.html.twig', [
            'ticket_type' => $ticketType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ticket_types_delete', methods: ['POST'])]
    public function delete(Request $request, TicketTypes $ticketType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticketType->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ticketType);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ticket_types_index', [], Response::HTTP_SEE_OTHER);
    }
}
