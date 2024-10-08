<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Entity\TicketTypes;
use App\Entity\Users;
use App\Form\TicketsType;
use App\Service\GorgiasApiService;
use App\Service\TicketsService;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CronController extends AbstractController
{
    #[Route('/cron_tickets', name: 'app_cron')]
    public function index(EntityManagerInterface $entityManager, GorgiasApiService $gorgiasApiService, TicketsService $ticketsService): Response
    {
        $limit = 100; // Nombre maximum de tickets par requête
        $totalTickets = 1000; // Nombre total de tickets à récupérer
        $tickets = [];
        $cursor = null;
//
//        for ($i = 0; $i < ($totalTickets / $limit); $i++) {
//            // Définir les paramètres de la requête
//            $queryParams = [
//                'cursor' => $cursor,
//                'limit' => $limit,
//                'order_by' => 'created_datetime:desc', // Récupérer les derniers tickets en premier
//            ];
//
//            // Récupérer les tickets
//            $data = $gorgiasApiService->getTickets($queryParams);
//
//            // Ajouter les tickets récupérés à la liste
//            if (!empty($data['data'])) {
//                $tickets = array_merge($tickets, $data['data']);
//            }
//
//            // Vérifier s'il y a un curseur pour continuer à récupérer les tickets
//            $cursor = $data['meta']['next_cursor'] ?? null;
//
//            // Pause pour éviter les dépassements de taux
//            sleep(2); // Ajustez la durée de la pause en fonction de la limite de taux
//
//            // Sortir de la boucle si aucun curseur n'est retourné
//            if (null === $cursor) {
//                break;
//            }
//
//            // Récupérer le curseur pour la pagination
////            $cursor = $data['meta']['next_cursor'] ?? null;
//        }
//
//        // Traitement des tickets (enregistrement dans la base de données, etc.)
//        foreach ($tickets as $ticketData) {
//            // Vérifier si le ticket existe déjà dans la base de données
//            $existingTicket = $entityManager->getRepository(Tickets::class)->findOneBy(['gorgiasTicketId' => $ticketData['id']]);
//            if (!$existingTicket) {
//                $ticket = new Tickets();
//                $ticket->setGorgiasTicketId($ticketData['id']);
////                $ticket->setSubject($ticketData['subject'] ?? null);
//                $ticket->setPriority($ticketData['priority'] ?? null);
//                $ticket->setCreatedAt(new \DateTimeImmutable($ticketData['created_datetime']));
//                $ticket->setUpdatedAt(new \DateTimeImmutable($ticketData['updated_datetime']));
//                $ticket->setChannel($ticketData['channel'] ?? null);
//                $ticket->setVia($ticketData['via'] ?? null);
//                $ticket->setCustomer($ticketData['customer']['email'] ?? "null");
//                $ticket->setActive($ticketData['customer']['active'] ?? false);
//                $ticket->setCreatedDatetime(new \DateTimeImmutable($ticketData['created_datetime']));
//                $ticket->setOpenedDatetime(new \DateTimeImmutable($ticketData['opened_datetime']));
//                $ticket->setLastReceivedMessageDatetime(new \DateTimeImmutable($ticketData['last_received_message_datetime']));
//                $ticket->setLastMessageDatetime(new \DateTimeImmutable($ticketData['last_message_datetime']));
////                $ticket->setCountryCode("");
////                $ticket->setContactReason("");
//
//                $ticketType = !empty($ticketData['integrations']) ? $ticketData['integrations'][0]['type'] : null;
//                $existingTypeTicket = $entityManager->getRepository(TicketTypes::class)->findOneBy(['type' => $ticketType]);
//
//                if ($existingTypeTicket) {
//                    $ticket->setType($existingTypeTicket);
//                } elseif (!$existingTypeTicket && !empty($ticketData['integrations'])) {
//                    $ticketType = new TicketTypes();
//                    $ticketType->setType($ticketData['integrations'][0]['type']);
//                    $ticketType->setName($ticketData['integrations'][0]['type']);
//                    $entityManager->persist($ticketType);
//                    $entityManager->flush();
//
//                    $ticket->setType($ticketType);
//                } else {
//                    $ticket->setType(null);
//                }
//
//                $entityManager->persist($ticket);
//            }
//        }
//
//        $entityManager->flush();

        $ticketsService->updateTickets();
        return $this->render('cron/index.html.twig', [
            'controller_name' => 'CronController',
//            'data' => $tickets,
        ]);
    }

    #[Route('/cron_user', name: 'app_cron_user')]
    public function user(EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $page = 1;
        $hasMore = true;
        $usersData = [];

        $response = $client->request('GET', 'https://manucurist.gorgias.com/api/users?limit=100&order_by=created_datetime%3Adesc', [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic aWNocmFrLmJhc3RpQG1hbnVjdXJpc3QuY29tOjg0MzE4MDllNmY0MTVhNzAxMzQ1NTY0YjJkNTgyNTYzMzNjOTBmZGQ2MDRkNGZiMGFmNWEwZmE2MzdiMjk5YmE=',
            ],
        ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            if (!empty($data['data'])) {
                $usersData = array_merge($usersData, $data['data']);
                $page++;
            } else {
                $hasMore = false;
            }

        // Traiter les données ici, par exemple enregistrer dans la base de données
        foreach ($usersData as $userData) {
            // Vérifier si l'utilisateur existe déjà
            $existingUser = $entityManager->getRepository(Users::class)->findOneBy(['gorgiasUserId' => $userData['id']]);
            if (!$existingUser) {
                $user = new Users();
                $user->setGorgiasUserId($userData['id']);
                $user->setEmail($userData['email']);
                $user->setName($userData['name']);
                $user->setPassword($userData['name']);
                $user->setCreatedAt(new \DateTimeImmutable($userData['created_datetime']));
                $user->setUpdatedAt(new \DateTimeImmutable($userData['updated_datetime']));
                $user->setRoles([$userData['role']['name']]); // Assignez le rôle Gorgias à l'utilisateur

                // Assigne les autres propriétés nécessaires ici
                // ...

                $entityManager->persist($user);
            }
        }

        $entityManager->flush();

        return $this->render('cron/index.html.twig', [
            'controller_name' => 'CronController',
            'data' => $usersData,
        ]);
    }
}