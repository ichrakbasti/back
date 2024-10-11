<?php

namespace App\Entity;

use App\Repository\MarketsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarketsRepository::class)]
class Markets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $countryCode = null;

    #[ORM\Column(length: 255)]
    private ?string $countryName = null;

    #[ORM\Column(length: 255)]
    private ?string $market = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function setCountryName(?string $countryName): void
    {
        $this->countryName = $countryName;
    }

    public function getMarket(): ?string
    {
        return $this->market;
    }

    public function setMarket(?string $market): void
    {
        $this->market = $market;
    }


}
