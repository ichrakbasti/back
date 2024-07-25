<?php

namespace App\Controller;

use App\Entity\Tickets;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CronController extends AbstractController
{
    #[Route('/cron', name: 'app_cron')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $client = new Client();

        // Remplace l'URL et l'autorisation par les tiennes
        $response = $client->request('GET', 'https://manucurist.gorgias.com/api/tickets?limit=50&order_by=created_datetime%3Adesc', [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic aWNocmFrLmJhc3RpQG1hbnVjdXJpc3QuY29tOjg0MzE4MDllNmY0MTVhNzAxMzQ1NTY0YjJkNTgyNTYzMzNjOTBmZGQ2MDRkNGZiMGFmNWEwZmE2MzdiMjk5YmE=',
            ],
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true);

        // Traiter les données ici, par exemple enregistrer dans la base de données
        foreach ($data["data"] as $ticketData) {
            // Vérifier si le ticket existe déjà
            $existingTicket = $entityManager->getRepository(Tickets::class)->findOneBy(['gorgiasTicketId' => $ticketData['id']]);
            if (!$existingTicket) {
                $ticket = new Tickets();
                $ticket->setGorgiasTicketId($ticketData['id']);
                $ticket->setSubject($ticketData['subject']);
                $ticket->setPriority($ticketData['priority'] ?? null);
                $ticket->setCreatedAt(new \DateTimeImmutable($ticketData['created_datetime']));
                $ticket->setUpdatedAt(new \DateTimeImmutable($ticketData['updated_datetime']));
                $ticket->setChannel($ticketData['channel'] ?? null);
                $ticket->setVia($ticketData['via'] ?? null);
                $ticket->setCustomer($ticketData['customer']['email'] ?? "null");
                $ticket->setActive($ticketData['customer']['active'] ?? false);
                $ticket->setCountryCode(/*$ticketData['customer']['integrations']['default_address']['country_code'] ??*/ "null");
                $ticket->setCreatedDatetime(new \DateTimeImmutable($ticketData['created_datetime']));
                $ticket->setOpenedDatetime(new \DateTimeImmutable($ticketData['opened_datetime']));
                $ticket->setLastReceivedMessageDatetime(new \DateTimeImmutable($ticketData['last_received_message_datetime']));
                $ticket->setLastMessageDatetime(new \DateTimeImmutable($ticketData['last_message_datetime']));

                // Assigne les autres propriétés nécessaires ici
                // ...

                $entityManager->persist($ticket);
            }
        }

        $entityManager->flush();

        return $this->render('cron/index.html.twig', [
            'controller_name' => 'CronController',
            'data' => $data["data"],
        ]);
    }

}
