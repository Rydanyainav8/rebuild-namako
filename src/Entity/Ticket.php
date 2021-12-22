<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 * @ORM\Table(name="ticket", indexes={@ORM\Index(columns={"numero"}, flags={"fulltext"})})
 */
class Ticket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $qr;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tickets")
     */
    private $badge;

    /**
     * @ORM\ManyToOne(targetEntity=Carnet::class, inversedBy="tickets", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $carnet;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getQr(): ?string
    {
        return $this->qr;
    }

    public function setQr(string $qr): self
    {
        $this->qr = $qr;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getBadge(): ?User
    {
        return $this->badge;
    }

    public function setBadge(?User $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    public function getCarnet(): ?Carnet
    {
        return $this->carnet;
    }

    public function setCarnet(?Carnet $carnet): self
    {
        $this->carnet = $carnet;

        return $this;
    }
}
