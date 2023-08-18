<?php

namespace Aatis\FixturesBundle\Entity;

use Aatis\FixturesBundle\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'persons')]
    private ?Company $company = null;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: ProjectPersonAssoc::class)]
    private Collection $projectPersonAssocs;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Car::class)]
    private Collection $cars;

    #[ORM\OneToMany(mappedBy: 'person', targetEntity: House::class)]
    private Collection $houses;

    public function __construct()
    {
        $this->projectPersonAssocs = new ArrayCollection();
        $this->cars = new ArrayCollection();
        $this->houses = new ArrayCollection();
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, ProjectPersonAssoc>
     */
    public function getProjectPersonAssocs(): Collection
    {
        return $this->projectPersonAssocs;
    }

    public function addProjectPersonAssoc(ProjectPersonAssoc $projectPersonAssoc): static
    {
        if (!$this->projectPersonAssocs->contains($projectPersonAssoc)) {
            $this->projectPersonAssocs->add($projectPersonAssoc);
            $projectPersonAssoc->setPerson($this);
        }

        return $this;
    }

    public function removeProjectPersonAssoc(ProjectPersonAssoc $projectPersonAssoc): static
    {
        if ($this->projectPersonAssocs->removeElement($projectPersonAssoc)) {
            // set the owning side to null (unless already changed)
            if ($projectPersonAssoc->getPerson() === $this) {
                $projectPersonAssoc->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Car>
     */
    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setPerson($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            // set the owning side to null (unless already changed)
            if ($car->getPerson() === $this) {
                $car->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, House>
     */
    public function getHouses(): Collection
    {
        return $this->houses;
    }

    public function addHouse(House $house): static
    {
        if (!$this->houses->contains($house)) {
            $this->houses->add($house);
            $house->setPerson($this);
        }

        return $this;
    }

    public function removeHouse(House $house): static
    {
        if ($this->houses->removeElement($house)) {
            // set the owning side to null (unless already changed)
            if ($house->getPerson() === $this) {
                $house->setPerson(null);
            }
        }

        return $this;
    }
}
