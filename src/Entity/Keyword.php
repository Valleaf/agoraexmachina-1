<?php

namespace App\Entity;

use App\Repository\KeywordRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Les mot-clés servent à donner une traversée du site alternative au lieu de la structure habituelle Catégorie =>
 * Thème => Atelier
 * @ORM\Entity(repositoryClass=KeywordRepository::class)
 */
class Keyword
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int Identifiant dans la BDD
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string Nom du mot-clé
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Workshop::class, mappedBy="keywords",cascade={"persist"})
     * @var Collection|Workshop[] Les ateliers associés à ce mot-clé
     */
    private $workshops;

    public function __construct()
    {
        $this->workshops = new ArrayCollection();
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

    /**
     * @return Collection|Workshop[]
     */
    public function getWorkshops(): Collection
    {
        return $this->workshops;
    }

    public function addWorkshop(Workshop $workshop): self
    {
        if (!$this->workshops->contains($workshop)) {
            $this->workshops[] = $workshop;
            $workshop->addKeyword($this);
        }

        return $this;
    }

    public function removeWorkshop(Workshop $workshop): self
    {
        if ($this->workshops->removeElement($workshop)) {
            $workshop->removeKeyword($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }


}
