<?php

namespace App\Entity;

use App\Repository\TicketsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketsRepository::class)]
class Tickets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $gorgiasTicketId = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?Users $userId = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?TicketStatuses $status = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?TicketTypes $type = null;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(length: 255)]
    private ?string $priority = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Tags>
     */
    #[ORM\OneToMany(targetEntity: Tags::class, mappedBy: 'tickets')]
    private Collection $tag;

    #[ORM\Column(length: 255)]
    private ?string $channel = null;

    #[ORM\Column(length: 255)]
    private ?string $via = null;

    #[ORM\Column(length: 255)]
    private ?string $customer = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255)]
    private ?string $countryCode = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdDatetime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $openedDatetime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastReceivedMessageDatetime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastMessageDatetime = null;

    public function __construct()
    {
        $this->tag = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGorgiasTicketId(): ?int
    {
        return $this->gorgiasTicketId;
    }

    public function setGorgiasTicketId(int $gorgiasTicketId): static
    {
        $this->gorgiasTicketId = $gorgiasTicketId;

        return $this;
    }


    public function getUserId(): ?Users
    {
        return $this->userId;
    }

    public function setUserId(?Users $userId): static
    {
        $this->userId = $userId;

        return $this;
    }
    public function getStatus(): ?TicketStatuses
    {
        return $this->status;
    }

    public function setStatus(?TicketStatuses $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): ?TicketTypes
    {
        return $this->type;
    }

    public function setType(?TicketTypes $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(Tags $tag): static
    {
        if (!$this->tag->contains($tag)) {
            $this->tag->add($tag);
            $tag->setTickets($this);
        }

        return $this;
    }

    public function removeTag(Tags $tag): static
    {
        if ($this->tag->removeElement($tag)) {
            // set the owning side to null (unless already changed)
            if ($tag->getTickets() === $this) {
                $tag->setTickets(null);
            }
        }

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(string $channel): static
    {
        $this->channel = $channel;

        return $this;
    }

    public function getVia(): ?string
    {
        return $this->via;
    }

    public function setVia(string $via): static
    {
        $this->via = $via;

        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): static
    {
        $this->countryCode = $countryCode;

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

    public function getOpenedDatetime(): ?\DateTimeImmutable
    {
        return $this->openedDatetime;
    }

    public function setOpenedDatetime(\DateTimeImmutable $openedDatetime): static
    {
        $this->openedDatetime = $openedDatetime;

        return $this;
    }

    public function getLastReceivedMessageDatetime(): ?\DateTimeImmutable
    {
        return $this->lastReceivedMessageDatetime;
    }

    public function setLastReceivedMessageDatetime(\DateTimeImmutable $lastReceivedMessageDatetime): static
    {
        $this->lastReceivedMessageDatetime = $lastReceivedMessageDatetime;

        return $this;
    }

    public function getLastMessageDatetime(): ?\DateTimeImmutable
    {
        return $this->lastMessageDatetime;
    }

    public function setLastMessageDatetime(\DateTimeImmutable $lastMessageDatetime): static
    {
        $this->lastMessageDatetime = $lastMessageDatetime;

        return $this;
    }
}