<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InscriptionRepository::class)
 */
class Inscription
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etudiant;

    /**
     * @ORM\ManyToOne(targetEntity=Cours::class, inversedBy="inscriptions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cours;

    /**
     * @ORM\ManyToMany(targetEntity=Exercice::class)
     */
    private $done_exercices;

    public function __construct()
    {
        $this->done_exercices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtudiant(): ?User
    {
        return $this->etudiant;
    }

    public function setEtudiant(?User $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    /**
     * @return Collection|Exercice[]
     */
    public function getDoneExercices(): Collection
    {
        return $this->done_exercices;
    }

    public function isDoneExercice(Exercice $doneExercice): bool
    {
        return $this->done_exercices->contains($doneExercice);
    }

    public function addDoneExercice(Exercice $doneExercice): self
    {
        if (!$this->done_exercices->contains($doneExercice)) {
            $this->done_exercices[] = $doneExercice;
        }

        return $this;
    }
}
