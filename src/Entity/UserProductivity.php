<?php

namespace App\Entity;

use App\Repository\UserProductivityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProductivityRepository::class)]
class UserProductivity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'productivity')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Users $user = null;

    #[ORM\Column]
    private ?int $estimatedOnlineTime = null; // en minutes

    #[ORM\Column]
    private ?float $occupancyRate = null; // pourcentage

    #[ORM\Column]
    private ?float $averageSurveyScore = null;

    #[ORM\Column]
    private ?int $surveyCount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getEstimatedOnlineTime(): ?int
    {
        return $this->estimatedOnlineTime;
    }

    public function setEstimatedOnlineTime(int $estimatedOnlineTime): static
    {
        $this->estimatedOnlineTime = $estimatedOnlineTime;

        return $this;
    }

    public function getOccupancyRate(): ?float
    {
        return $this->occupancyRate;
    }

    public function setOccupancyRate(float $occupancyRate): static
    {
        $this->occupancyRate = $occupancyRate;

        return $this;
    }

    public function getAverageSurveyScore(): ?float
    {
        return $this->averageSurveyScore;
    }

    public function setAverageSurveyScore(float $averageSurveyScore): static
    {
        $this->averageSurveyScore = $averageSurveyScore;

        return $this;
    }

    public function getSurveyCount(): ?int
    {
        return $this->surveyCount;
    }

    public function setSurveyCount(int $surveyCount): static
    {
        $this->surveyCount = $surveyCount;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

}
