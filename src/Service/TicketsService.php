<?php

namespace App\Service;

use App\Entity\Tickets;
use Doctrine\ORM\EntityManagerInterface;

class TicketsService
{
    private $entityManager;
    private $gorgiasService;

    public function __construct(EntityManagerInterface $entityManager, GorgiasApiService $gorgiasService)
    {
        $this->entityManager = $entityManager;
        $this->gorgiasService = $gorgiasService;
    }

    public function updateTickets(): void
    {

        // Récupérer les tickets des 15 derniers jours sans contactReason ou countryCode
        $tickets = $this->entityManager->getRepository(Tickets::class)->createQueryBuilder('t')
            ->where('t.createdDatetime >= :date')
            ->andWhere('t.contactReason IS NULL OR t.countryCode IS NULL')
            ->setParameter('date', new \DateTime('-1 days'))
            ->setMaxResults(500)
            ->getQuery()
            ->getResult();

        foreach ($tickets as $ticket) {
            $ticketId = $ticket->getGorgiasTicketId();
            $customFields = $this->gorgiasService->getTicketsCustomField($ticketId);

            // Vérification de l'existence des custom fields
            if (isset($customFields['custom_fields']) && is_array($customFields['custom_fields'])) {
                $fields = $customFields['custom_fields'];

                // Vérification et mise à jour des champs spécifiques
                if (isset($fields["26"]['value']) && !empty($fields["26"]['value'])) {
                    $ticket->setContactReason($fields[26]['value']);
                }

                if (isset($fields["27"]['value']) && !empty($fields["27"]['value'])) {
                    $ticket->setCountryCode($fields[27]['value']);
                }
            }

            $this->entityManager->persist($ticket);
        }

        $this->entityManager->flush();
    }

}