<?php

namespace App\Entity;

use App\Repository\OeuvresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OeuvresRepository::class)]
class Oeuvres
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $idoeuvre = null;

    #[ORM\Column]
    #[Assert\Positive(message: "The price must be a positive number.")]
    #[Assert\NotBlank(message: "The Price is required.")]
    private ?int $prix = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The title is required.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "The title cannot be longer than {{ limit }} characters."
    )]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The category is required.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "The category cannot be longer than {{ limit }} characters."
    )]
    private ?string $categorie = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "The description is required.")]
    #[Assert\Length(
        max: 1000,
        maxMessage: "The description cannot be longer than {{ limit }} characters."
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $img = null;

    #[ORM\Column]
    private ?int $iduser = null;

    #[ORM\OneToMany(targetEntity: Reservations::class, mappedBy: 'idReservation')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getIdoeuvre(): ?int
    {
        return $this->idoeuvre;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function setIduser(int $iduser): self
    {
        $this->iduser = $iduser;

        return $this;
    }

    /**
     * @return Collection<int, Reservations>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservations $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setIdReservation($this);
        }

        return $this;
    }

    public function removeReservation(Reservations $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getIdReservation() === $this) {
                $reservation->setIdReservation(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return  $this->titre ;
    }


}
