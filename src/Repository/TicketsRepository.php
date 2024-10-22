<?php

namespace App\Repository;

use App\Entity\Tickets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tickets>
 */
class TicketsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tickets::class);
    }

    public function getTotalTicketsByType(string $ticketType): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->join('t.type', 'tt') // Jointure avec l'entité TicketTypes
            ->where('tt.type = :ticketType') // Filtre sur le type de ticket passé en paramètre
            ->setParameter('ticketType', $ticketType)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalTicketsPerMonthAndType(): array
    {
        return $this->createQueryBuilder('t')
            ->select("EXTRACT(DAY FROM t.createdAt) as day, EXTRACT(MONTH FROM t.createdAt) as month, EXTRACT(YEAR FROM t.createdAt) as year, tt.type as ticketType, COUNT(t.id) as total")
            ->join('t.type', 'tt') // Join with TicketTypes entity
            ->groupBy('year', 'month', 'day', 'tt.type')
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'ASC')
            ->addOrderBy('day', 'ASC') // Add ordering by day
            ->getQuery()
            ->getResult();
    }

    public function getTotalTicketsPerMarketByDay(): array
    {
        return $this->createQueryBuilder('t')
            ->select("EXTRACT(DAY FROM t.createdAt) AS day, EXTRACT(MONTH FROM t.createdAt) AS month, EXTRACT(YEAR FROM t.createdAt) AS year, m.market AS market, COUNT(t.id) AS total")
            ->join('t.market', 'm') // Jointure avec l'entité Markets
            ->groupBy('year, month, day, m.market') // Regroupement par jour, mois, année et marché
            ->orderBy('year', 'ASC')
            ->addOrderBy('month', 'ASC')
            ->addOrderBy('day', 'ASC') // Tri par jour
            ->getQuery()
            ->getResult();
    }

    public function getTotalTicketsReceived(): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalTicketsProcessed(): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->leftJoin('t.status', 'status')
            ->where('status.statusName = :statusName')
            ->setParameter('statusName', 'closed')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTicketStatisticsByUserCM(): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select(
                'u.id AS userId',
                'u.email AS fullName',
                'tt.name AS ticketType',
                'COUNT(t.id) AS ticketCount'
            )
            ->join('t.userId', 'u')        // Join with Users entity to get user info
            ->join('t.type', 'tt')         // Join with TicketTypes entity to get ticket type info
            ->join('t.team', 'team')        // Join with Teams entity to filter by team name
            ->where('team.name = :teamName') // Add condition for team name
            ->setParameter('teamName', 'CM Tunis')
            ->groupBy('u.id, tt.name')     // Group by user and ticket type
            ->orderBy('u.id');

        $result = $qb->getQuery()->getResult();
        // Organize the data by user with total counts for each ticket type
        $statistics = [];
        foreach ($result as $row) {
            $userId = $row['userId'];
            $fullName = $row['fullName'];
            $ticketType = $row['ticketType'];
            $ticketCount = $row['ticketCount'];

            // Initialize user data if not already set
            if (!isset($statistics[$userId])) {
                $statistics[$userId] = [
                    'id' => $userId,
                    'user_name' => $fullName,
                    'facebook' => 0,
                    'instagram' => 0,
                    'comment_instagram' => 0,
                    'tiktok' => 0,
                    'gorgias' => 0,
                    'yotpo' => 0,
                    'total_CM' => 0,
                ];
            }

            // Map ticket type counts to the correct fields
            switch ($ticketType) {
                case 'facebook':
                    $statistics[$userId]['facebook'] = $ticketCount;
                    break;
                case 'instagram':
                    $statistics[$userId]['instagram'] = $ticketCount;
                    break;
                case 'instagram_comment':
                    $statistics[$userId]['comment_instagram'] = $ticketCount;
                    break;
                case 'tiktok':
                    $statistics[$userId]['tiktok'] = $ticketCount;
                    break;
                case 'gorgias':
                    $statistics[$userId]['gorgias'] = $ticketCount;
                    break;
                case 'yotpo':
                    $statistics[$userId]['yotpo'] = $ticketCount;
                    break;
            }

            // Update the total count for CM tickets
            $statistics[$userId]['total_CM'] += $ticketCount;
        }

        return array_values($statistics); // Re-index the array for clean output
    }

    public function getTicketStatisticsByUserCustomerCare(): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select(
                'u.id AS userId',
                'u.email AS fullName',
                'tt.name AS ticketType',
                'COUNT(t.id) AS ticketCount'
            )
            ->join('t.userId', 'u')        // Join with Users entity to get user info
            ->join('t.type', 'tt')         // Join with TicketTypes entity to get ticket type info
            ->join('t.team', 'team')       // Join with Teams entity to filter by team name
            ->where('team.name = :teamName') // Filter by the team name 'Customer Care'
            ->setParameter('teamName', 'Customer care')
            ->groupBy('u.id, tt.name')     // Group by user and ticket type
            ->orderBy('u.id');

        $result = $qb->getQuery()->getResult();

        // Organize the data by user with total counts for each ticket type
        $statistics = [];
        foreach ($result as $row) {
            $userId = $row['userId'];
            $fullName = $row['fullName'];
            $ticketType = $row['ticketType'];
            $ticketCount = $row['ticketCount'];

            // Initialize user data if not already set
            if (!isset($statistics[$userId])) {
                $statistics[$userId] = [
                    'id' => $userId,
                    'user_name' => $fullName,
                    'mail' => 0,
                    'whatsapp' => 0,
                    'chat' => 0,
                    'ticketCount' => 0,  // Total for Customer Care tickets
                    'nbr_survey' => 0, // Initialize survey data
                    'average_survey_score' => 0.0,
                ];
            }

            // Map ticket type counts to the correct fields
            switch ($ticketType) {
                case 'mail':
                    $statistics[$userId]['mail'] = $ticketCount;
                    break;
                case 'whatsapp':
                    $statistics[$userId]['whatsapp'] = $ticketCount;
                    break;
                case 'chat':
                    $statistics[$userId]['chat'] = $ticketCount;
                    break;
            }

            // Update the total count for Customer Care tickets
            $statistics[$userId]['ticketCount'] += $ticketCount;
        }

        return array_values($statistics); // Re-index the array for clean output
    }


}
