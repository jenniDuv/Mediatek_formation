<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity("name")
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\NotNull()
     * @Assert\Length(min=2, max=50)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Formation::class, mappedBy="categories")
     */
    private $formations;

    /**
     * @ORM\ManyToMany(targetEntity=Formation::class, mappedBy="categories")
     */
    private $formation;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
        $this->formation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
            $formation->addCategory($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->removeElement($formation)) {
            $formation->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormationAdd(): Collection
    {
        return $this->formation;
    }

    public function addFormationAdd(Formation $formation): self
    {
        if (!$this->formation->contains($formation)) {
            $this->formation[] = $formation;
            $formation->addCategory($this);
        }

        return $this;
    }

    public function removeFormationAdd(Formation $formation): self
    {
        if ($this->formation->removeElement($formation)) {
            $formation->removeCategory($this);
        }

        return $this;
    }
}
