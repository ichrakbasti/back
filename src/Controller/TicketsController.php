<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Entity\TicketTypes;
use App\Form\TicketsType;
use App\Repository\TicketsRepository;
use App\Service\GorgiasApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tickets')]
class TicketsController extends AbstractController
{

    #[Route('/', name: 'app_tickets_index', methods: ['GET'])]
    public function index(TicketsRepository $ticketsRepository): Response
    {
        return $this->render('tickets/index.html.twig', [
            'tickets' => $ticketsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_tickets_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Tickets();
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('app_tickets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tickets/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tickets_show', methods: ['GET'])]
    public function show(Tickets $ticket): Response
    {
        return $this->render('tickets/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tickets_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tickets $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketsType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tickets_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tickets/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tickets_delete', methods: ['POST'])]
    public function delete(Request $request, Tickets $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tickets_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/dashboard/total-chat', name: 'dashboard_total_chat')]
    public function getTotalChat(TicketsRepository $ticketsRepository): JsonResponse
    {
        $total = $ticketsRepository->getTotalTicketsByType('chat');

        return new JsonResponse(['total' => $total]);
    }

    #[Route('/api/dashboard/total-email', name: 'dashboard_total_email')]
    public function getTotalEmail(TicketsRepository $ticketsRepository): JsonResponse
    {
        $total = $ticketsRepository->getTotalTicketsByType('email');

        return new JsonResponse(['total' => $total]);
    }

    #[Route('/api/dashboard/total-facebook', name: 'dashboard_total_facebook')]
    public function getTotalFacebook(TicketsRepository $ticketsRepository): JsonResponse
    {
        $total = $ticketsRepository->getTotalTicketsByType('facebook');

        return new JsonResponse(['total' => $total]);
    }

    #[Route('/api/dashboard/total-app', name: 'dashboard_total_app')]
    public function getTotalApp(TicketsRepository $ticketsRepository): JsonResponse
    {
        $total = $ticketsRepository->getTotalTicketsByType('app');

        return new JsonResponse(['total' => $total]);
    }

    #[Route('/api/dashboard/total-http', name: 'dashboard_total_http')]
    public function getTotalHttp(TicketsRepository $ticketsRepository): JsonResponse
    {
        $total = $ticketsRepository->getTotalTicketsByType('http');

        dd($total);
        return new JsonResponse(['total' => $total]);
    }

    #[Route('/api/dashboard/total-yotpo', name: 'dashboard_total_yotpo')]
    public function getTotalYotpo(TicketsRepository $ticketsRepository): JsonResponse
    {
        $total = $ticketsRepository->getTotalTicketsByType('yotpo');


        return new JsonResponse(['total' => $total]);
    }

    #[Route('/api/dashboard/total-tickets-per-month', name: 'dashboard_total_tickets_per_month')]
    public function getTotalTicketsPerMonth(TicketsRepository $ticketsRepository): JsonResponse
    {
        $data = $ticketsRepository->getTotalTicketsPerMonth();

        dd($data);
        return new JsonResponse($data);
    }
     #[Route('/api/dashboard/statistic', name: 'dashboard_statistic')]
    public function getstats(TicketsRepository $ticketsRepository, GorgiasApiService $gorgiasApiService): JsonResponse
    {
        $params = [
            'name' => 100,  // Le maximum permis par l'API
            'order_by' => 'created_datetime:desc',
        ];

        $stats = [];
        $cursor = null;

        do {
            if ($cursor) {
                $params['cursor'] = $cursor;  // Ajout du curseur pour passer à la page suivante
            }

            $stat = $gorgiasApiService->fetchStatistic($params);


            $stats = array_merge($stats, $stat['data']);


        } while ($cursor);  // Continuer tant qu'il y a une page suivante

        var_dump($stats);
//        foreach ($stats as $integration) {
//            $ticketType = new TicketTypes();
//            $ticketType->setIntegrationId($integration['id']);
//            $ticketType->setName($integration['name']);
//            $ticketType->setType($integration['type']);
//
//            $this->entityManager->persist($ticketType);
//        }
//
//        $this->entityManager->flush();

        return new JsonResponse($stats);
    }

    public function getTotalTicketsPerMarket(TicketsRepository $ticketsRepository): JsonResponse
    {
        $data = $ticketsRepository->getTotalTicketsPerMarket();

        return new JsonResponse($data);
    }



}
