<?php

namespace App\Controller;

use App\Entity\Tickets;
use App\Entity\TicketTypes;
use App\Form\TicketsType;
use App\Repository\TicketsRepository;
use App\Repository\UserProductivityRepository;
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

    #[Route('/api/dashboard/ticket-total-stats', name: 'dashboard_ticket_stats')]
    public function getTicketTotalStats(TicketsRepository $ticketsRepository): JsonResponse
    {
        $data = [
            'total_received' => $ticketsRepository->getTotalTicketsReceived(),
            'total_processed' => $ticketsRepository->getTotalTicketsProcessed(),
            'total_email' => $ticketsRepository->getTotalTicketsByType('email'),
            'total_chat' => $ticketsRepository->getTotalTicketsByType('chat'),
            'total_whatsapp' => $ticketsRepository->getTotalTicketsByType('whatsapp')
        ];

        return new JsonResponse($data);
    }


    #[Route('/api/dashboard/total-tickets-per-market', name: 'dashboard_total_tickets_per_market')]
    public function getTotalTicketsPerMarketByDay(TicketsRepository $ticketsRepository): JsonResponse
    {
        $ticket_data = $ticketsRepository->getTotalTicketsPerMarketByDay();

        $data_by_market = [];
        $date_categories = [];

        foreach ($ticket_data as $row) {
            // Extraire jour, mois, année, marché et total
            $day = $row['day'];
            $month = $row['month'];
            $year = $row['year'];
            $market = $row['market'];
            $total = $row['total'];

            $date_str = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            if (!in_array($date_str, $date_categories)) {
                $date_categories[] = $date_str;
            }

            // Si le marché n'est pas dans data_by_market, initialiser un tableau vide
            if (!isset($data_by_market[$market])) {
                $data_by_market[$market] = [];
            }

            $data_by_market[$market][] = $total;
        }

        sort($date_categories);

        $series_data = [];

        foreach ($data_by_market as $market => $totals) {
            $series_item = [
                'name' => $market,
                'data' => $totals
            ];

            $series_data[] = $series_item;
        }

        $chart_data = [
            'series' => $series_data,
            'xaxis' => [
                'type' => 'datetime',
                'categories' => $date_categories
            ]
        ];

        return new JsonResponse($chart_data);
    }


    #[Route('/api/dashboard/total-tickets-per-month', name: 'dashboard_total_tickets_per_month')]
    public function getTotalTicketsPerMonth(TicketsRepository $ticketsRepository): JsonResponse
    {

        $ticket_data = $ticketsRepository->getTotalTicketsPerMonthAndType();

        $data_by_product = [];

        $date_categories = [];

        foreach ($ticket_data as $row) {
            // 5. Extract day, month, year, ticketType, and total
            $day = $row['day'];
            $month = $row['month'];
            $year = $row['year'];
            $ticket_type = $row['ticketType'];
            $total = $row['total'];

            $date_str = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
            if (!in_array($date_str, $date_categories)) {
                $date_categories[] = $date_str;
            }

            // 7. If ticketType is not in data_by_product, initialize an empty array for it
            if (!isset($data_by_product[$ticket_type])) {
                $data_by_product[$ticket_type] = [];
            }

            $data_by_product[$ticket_type][] = $total;
        }

        sort($date_categories);

        $series_data = [];

        foreach ($data_by_product as $ticket_type => $totals) {
            $series_item = [
                'name' => $ticket_type,
                'data' => $totals
            ];

            $series_data[] = $series_item;
        }

        $chart_data = [
            'series' => $series_data,
            'xaxis' => [
                'type' => 'datetime',
                'categories' => $date_categories
            ]
        ];

        return new JsonResponse($chart_data);
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

    #[Route('/api/productivity/cm', name: 'get_productivity_cm', methods: ['post'])]
    public function getProductivityCM(Request $request, TicketsRepository $repository, UserProductivityRepository $userProductivityRepository): JsonResponse
    {
        $ticketData = $repository->getTicketStatisticsByUserCM();
        $productivityData = [];
        // Step 1: Get the raw JSON data from the request body
        $data = json_decode($request->getContent(), true);

        // Step 2: Extract start_date and end_date from the decoded JSON data
        $startDatePost = $data['start_date'] ?? null;
        $endDatePost = $data['end_date'] ?? null;

        // Debugging output to check the extracted dates
        // Step 2: Define default date range (last month) if no dates are provided
        $dateNow = new \DateTime(); // Current date
        $startDate = (clone $dateNow)->modify('first day of last month')->setTime(0, 0, 0); // First day of last month
        $endDate = (clone $dateNow)->modify('last day of last month')->setTime(23, 59, 59); // Last day of last month

        // Step 3: Override default dates if POST parameters are provided
        if ($startDatePost) {
            $startDate = \DateTime::createFromFormat('Y-m-d', $startDatePost)->setTime(0, 0, 0);
        }
        if ($endDatePost) {
            $endDate = \DateTime::createFromFormat('Y-m-d', $endDatePost)->setTime(23, 59, 59);
        }

        dump($startDate);
        dump($endDate);
        // Step 3: Loop through each user’s ticket data and gather additional productivity metrics
        foreach ($ticketData as $user) {
            $userId = $user['id'];

            $qb = $userProductivityRepository->createQueryBuilder('up')
                ->where('up.user = :userId')
                ->andWhere('up.dateDebut >= :startDate')
                ->andWhere('up.dateFin <= :endDate')
                ->setParameter('userId', $userId)
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);

            $productivityRecords = $qb->getQuery()->getResult();


            if ($productivityRecords) {
                foreach ($productivityRecords as $productivityRecord) {
                    // Gather and calculate the required metrics
                    $daysWorked = $productivityRecord->getDateDebut()->diff($productivityRecord->getDateFin())->days;
                    $estimatedOnlineTime = $productivityRecord->getEstimatedOnlineTime();
                    $occupancyPercentage = ($productivityRecord->getOccupancyRate() / 480) * 100;

                    // Collect the data for this user
                    $productivityData[] = [
                        'id' => $userId,
                        'user_name' => $user['user_name'],
                        'facebook' => $user['facebook'],
                        'instagram' => $user['instagram'],
                        'comment_instagram' => $user['comment_instagram'],
                        'tiktok' => $user['tiktok'],
                        'gorgias' => $user['gorgias'],
                        'yotpo' => $user['yotpo'],
                        'total_CM' => $user['total_CM'],
                        'nbr_days_worked' => $daysWorked,
                        'estimated_online_time' => $estimatedOnlineTime,
                        'occupancy_percentage' => $occupancyPercentage,
//                        'Obj à traiter CM' => $productivityRecord->getAverageSurveyScore(),
//                        'Taux d’attente Obj Nbr de msg CM' => $waitingRate,
                        'total_CM' => $user['total_CM'],
                    ];
                }
            }
        }
        return $this->json($productivityData);
    }
    #[Route('/api/productivity/customer-care', name: 'get_productivity_customer_care', methods: ['post'])]
    public function getProductivityCustomerCare(Request $request, TicketsRepository $repository, UserProductivityRepository $userProductivityRepository): JsonResponse
    {
        $ticketData = $repository->getTicketStatisticsByUserCustomerCare('Customer Care'); // On filtre par l'équipe Customer Care
//        dd($ticketData);
        $productivityData = [];
        $data = json_decode($request->getContent(), true);

        $startDatePost = $data['start_date'] ?? null;
        $endDatePost = $data['end_date'] ?? null;

        $dateNow = new \DateTime();
        $startDate = (clone $dateNow)->modify('first day of last month')->setTime(0, 0, 0);
        $endDate = (clone $dateNow)->modify('last day of last month')->setTime(23, 59, 59);

        if ($startDatePost) {
            $startDate = \DateTime::createFromFormat('Y-m-d', $startDatePost)->setTime(0, 0, 0);
        }
        if ($endDatePost) {
            $endDate = \DateTime::createFromFormat('Y-m-d', $endDatePost)->setTime(23, 59, 59);
        }

        foreach ($ticketData as $user) {
            $userId = $user['id'];

            $qb = $userProductivityRepository->createQueryBuilder('up')
                ->where('up.user = :userId')
                ->andWhere('up.dateDebut >= :startDate')
                ->andWhere('up.dateFin <= :endDate')
                ->setParameter('userId', $userId)
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);

            $productivityRecords = $qb->getQuery()->getResult();

            if ($productivityRecords) {
                foreach ($productivityRecords as $productivityRecord) {
                    $daysWorked = $productivityRecord->getDateDebut()->diff($productivityRecord->getDateFin())->days;
                    $estimatedOnlineTime = $productivityRecord->getEstimatedOnlineTime();
                    $occupancyPercentage = ($productivityRecord->getOccupancyRate() / 480) * 100;

                    $productivityData[] = [
                        'id' => $userId,
                        'user_name' => $user['user_name'],
                        'mail' => $user['mail'],            // Nombre de mails traités
                        'whatsapp' => $user['whatsapp'],    // Nombre de WhatsApp traités
                        'chat' => $user['chat'],            // Nombre de chats traités
                        'total_treated' => $user['ticketCount'],  // Total des tickets traités
                        'nbr_days_worked' => $daysWorked,
                        'estimated_online_time' => $estimatedOnlineTime,
                        'occupancy_percentage' => $occupancyPercentage,
                        'nbr_surveys' => $productivityRecord->getSurveyCount(),
                        'average_satisfaction_score' => $productivityRecord->getAverageSurveyScore(),
                    ];
                }
            }
        }
        return $this->json($productivityData);
    }



}
