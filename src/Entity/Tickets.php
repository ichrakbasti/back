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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $priority = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;



    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $via = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customer = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\ManyToOne(targetEntity: Markets::class, inversedBy: 'tickets')]
    private ?Markets $market = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdDatetime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $openedDatetime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastReceivedMessageDatetime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastMessageDatetime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contactReason = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?Teams $team = null;

    /**
     * @var Collection<int, Tags>
     */
    #[ORM\ManyToMany(targetEntity: Tags::class, inversedBy: 'tickets')]
    private Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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

    public function getContactReason(): ?string
    {
        return $this->contactReason;
    }

    public function setContactReason(string $contactReason): static
    {
        $this->contactReason = $contactReason;

        return $this;
    }

    public function getMarket(): ?Markets
    {
        return $this->market;
    }

    public function setMarket(?Markets $market): void
    {
        $this->market = $market;
    }

    public function getTeam(): ?Teams
    {
        return $this->team;
    }

    public function setTeam(?Teams $team): static
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tags $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }


}
