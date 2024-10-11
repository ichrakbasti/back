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
    public function getTotalTicketsPerMarket(): array
    {
        return $this->createQueryBuilder('t')
            ->select('m.code as market, COUNT(t.id) as total')
            ->join('t.market', 'm') // Jointure avec l'entité Markets
            ->groupBy('m.code')
            ->orderBy('total', 'DESC') // Trier par nombre de tickets décroissant
            ->getQuery()
            ->getResult();
    }



    //    /**
    //     * @return Tickets[] Returns an array of Tickets objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Tickets
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
