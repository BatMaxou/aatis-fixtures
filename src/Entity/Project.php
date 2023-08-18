<?php

namespace Aatis\FixturesBundle\Entity;

use Aatis\FixturesBundle\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbMin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: ProjectPersonAssoc::class)]
    private Collection $projectPersonAssocs;

    public function __construct()
    {
        $this->projectPersonAssocs = new ArrayCollection();
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

    public function getNbMin(): ?int
    {
        return $this->nbMin;
    }

    public function setNbMin(?int $nbMin): static
    {
        $this->nbMin = $nbMin;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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
            $projectPersonAssoc->setProject($this);
        }

        return $this;
    }

    public function removeProjectPersonAssoc(ProjectPersonAssoc $projectPersonAssoc): static
    {
        if ($this->projectPersonAssocs->removeElement($projectPersonAssoc)) {
            // set the owning side to null (unless already changed)
            if ($projectPersonAssoc->getProject() === $this) {
                $projectPersonAssoc->setProject(null);
            }
        }

        return $this;
    }
}
