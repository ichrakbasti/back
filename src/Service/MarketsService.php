<?php

namespace App\Service;

use App\Entity\Markets;
use Doctrine\ORM\EntityManagerInterface;

class MarketsService
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    // Fonction pour récupérer le marché correspondant au code de pays
    public function getMarketByCountryCode(string $countryCode): ?Markets
    {
        dump($countryCode);
        return $this->entityManager->getRepository(Markets::class)
            ->findOneBy(['countryName' => $countryCode]);
    }
}