<?php

namespace App\Entity;

use App\Repository\ReservationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationsRepository::class)]
class Reservations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column("idReservation")]
    private ?int $idReservation = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: "oeuvreID", referencedColumnName: "idoeuvre")]
    private ?Oeuvres $oeuvreID = null;


    #[ORM\Column("dateReservation",type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "The reservation date is required.")]
    #[Assert\GreaterThanOrEqual('today', message: "The reservation date cannot be in the past.")]
    private ?\DateTimeInterface $dateReservation = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    public function getIdReservation(): ?int
    {
        return $this->idReservation;
    }

    public function getOeuvreID(): ?Oeuvres
    {
        return $this->oeuvreID;
    }

    public function setOeuvreID(?Oeuvres $oeuvreID): self
    {
        $this->oeuvreID = $oeuvreID;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(?\DateTimeInterface $dateReservation): self
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }
}
