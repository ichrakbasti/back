<?php

namespace App\Entity;

use App\Repository\TeamsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamsRepository::class)]
class Teams
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $gorgiasTeamId = null;

    /**
     * @var Collection<int, Tickets>
     */
    #[ORM\OneToMany(targetEntity: Tickets::class, mappedBy: 'team')]
    private Collection $tickets;


    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Tickets>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Tickets $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setTeam($this);
        }

        return $this;
    }

    public function removeTicket(Tickets $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getTeam() === $this) {
                $ticket->setTeam(null);
            }
        }

        return $this;
    }

    public function getGorgiasTeamId(): ?int
    {
        return $this->gorgiasTeamId;
    }

    public function setGorgiasTeamId(?int $gorgiasTeamId): void
    {
        $this->gorgiasTeamId = $gorgiasTeamId;
    }

}
