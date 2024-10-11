<?php

namespace App\Service;

use App\Entity\Tickets;
use Doctrine\ORM\EntityManagerInterface;

class TicketsService
{
    private $entityManager;
    private $gorgiasService;
    private $marketsService;

    public function __construct(EntityManagerInterface $entityManager, GorgiasApiService $gorgiasService, MarketsService $marketsService)
    {
        $this->entityManager = $entityManager;
        $this->gorgiasService = $gorgiasService;
        $this->marketsService = $marketsService;
    }

    // Service ou contrôleur pour mettre à jour les tickets
    public function updateTickets(): void
    {
        // Récupérer les tickets des 15 derniers jours sans contactReason ou countryCode
        $tickets = $this->entityManager->getRepository(Tickets::class)->createQueryBuilder('t')
//            ->where('t.createdDatetime >= :date')
//            ->andWhere('t.contactReason IS NULL')
//            ->andWhere('t.market IS NULL')
//            ->andWhere('t.countryCode IS NOT NULL')
            ->andWhere('t.userId IS NULL')
//            ->setParameter('date', new \DateTime('-90 days'))
//            ->setMaxResults(1000)  // Limiter à 100 tickets par lot
            ->getQuery()
            ->getResult();
        foreach ($tickets as $ticket) {
            $ticketId = $ticket->getGorgiasTicketId();

            try {
                $customFields = $this->gorgiasService->getTicketsCustomField($ticketId);

                // Vérification des champs et mise à jour des données du ticket
                if (isset($customFields['custom_fields']) && is_array($customFields['custom_fields'])) {
                    $fields = $customFields['custom_fields'];

                    if (isset($fields["26"]['value']) && !empty($fields["26"]['value'])) {
                        $ticket->setContactReason($fields["26"]['value']);
                    }

                    if (isset($fields["27"]['value']) && !empty($fields["27"]['value'])) {
                        $ticket->setCountryCode($fields["27"]['value']);


                    }

                }

                $this->entityManager->persist($ticket);

                // Temporisation pour éviter le dépassement de la limite de débit
                usleep(200000);  // Pause de 0.2 secondes entre chaque requête

            } catch (\Exception $e) {
                if ($e->getCode() === 429) {
                    // Temporisation supplémentaire en cas de `429`
                    sleep(60);
                } else {
                    throw $e;  // Ré-entraîner l'exception si ce n'est pas une erreur de débit
                }
            }
//            dump($ticket->getCountryCode());
//            // Récupérer le market correspondant au countryCode
//            $market = $this->marketsService->getMarketByCountryCode($ticket->getCountryCode()?:"");
//
//            // Si le marché est trouvé, on l'affecte au ticket
//            if ($market) {
//                $ticket->setMarket($market);
//            }
//            else{
//                $ticket->setMarket($market = $this->marketsService->getMarketByCountryCode("Autre"));
//            }
        }

        $this->entityManager->flush();
    }



}