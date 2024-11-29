<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le nom de l\'événement doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom de l\'événement ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $name = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: 'La date est obligatoire')]
    #[Assert\Type("\DateTimeInterface")]
    #[Assert\GreaterThanOrEqual(
        'now',
        message: 'La date et l\'heure doivent être dans le futur'
    )]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message: 'La localisation est obligatoire')]
    private ?string $location = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La latitude est obligatoire')]
    #[Assert\Regex(
        pattern: '/^[-]?((([0-8]?[0-9])\.(\d+))|(90(\.0+)?))$/',
        message: 'La latitude doit être entre -90 et 90'
    )]
    private ?string $latitude = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La longitude est obligatoire')]
    #[Assert\Regex(
        pattern: '/^[-]?((([0-9]?[0-9]|1[0-7][0-9])\.(\d+))|(180(\.0+)?))$/',
        message: 'La longitude doit être entre -180 et 180'
    )]
    private ?string $longitude = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Participant::class, orphanRemoval: true)]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setEvent($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getEvent() === $this) {
                $participant->setEvent(null);
            }
        }

        return $this;
    }
} 