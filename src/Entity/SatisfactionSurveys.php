<?php

namespace App\Entity;

use App\Repository\SatisfactionSurveysRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SatisfactionSurveysRepository::class)]
class SatisfactionSurveys
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdDatetime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sentDatetime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $scoredDatetime = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Tickets $ticketId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getCreatedDatetime(): ?\DateTimeImmutable
    {
        return $this->createdDatetime;
    }

    public function setCreatedDatetime(\DateTimeImmutable $createdDatetime): static
    {
        $this->createdDatetime = $createdDatetime;

        return $this;
    }

    public function getSentDatetime(): ?\DateTimeImmutable
    {
        return $this->sentDatetime;
    }

    public function setSentDatetime(\DateTimeImmutable $sentDatetime): static
    {
        $this->sentDatetime = $sentDatetime;

        return $this;
    }

    public function getScoredDatetime(): ?\DateTimeImmutable
    {
        return $this->scoredDatetime;
    }

    public function setScoredDatetime(\DateTimeImmutable $scoredDatetime): static
    {
        $this->scoredDatetime = $scoredDatetime;

        return $this;
    }

    public function getTicketId(): ?Tickets
    {
        return $this->ticketId;
    }

    public function setTicketId(?Tickets $ticketId): static
    {
        $this->ticketId = $ticketId;

        return $this;
    }
}
