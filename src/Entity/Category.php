<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Les catégories permettent de ranger les thèmes et ateliers. Les utilisateurs peuvent être
 * souscrits à des catégories pour accéder à leur contenu. Les modérateurs et administrateurs restreints peuvent être
 * inscrits à des catégories pour pouvoir opérer dans ces catégories
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @UniqueEntity("name")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int Identifiant dans la BDD
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     * @Assert\NotBlank()
     * @var string Nom de la catégorie
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Theme::class, mappedBy="category",cascade={"remove"})
     * @var Collection|Theme[] Les thèmes associés à cette catégorie
     */
    private $themes;


    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="categories",cascade={"all"})
     * @var Collection|User[] Les utilisateurs souscrits à cette catégorie
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Request::class, mappedBy="category", orphanRemoval=true)
     * @var Collection|Request[] Les requêtes associés à une demande pour rejoindre cette catégorie
     */
    private $requests;

    public function __construct()
    {
        $this->themes = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->requests = new ArrayCollection();
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
     * @return Collection|Theme[]
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Theme $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->setCategory($this);
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        if ($this->themes->removeElement($theme)) {
            // set the owning side to null (unless already changed)
            if ($theme->getCategory() === $this) {
                $theme->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection|Request[]
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Request $request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            $request->setCategory($this);
        }

        return $this;
    }

    public function removeRequest(Request $request): self
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getCategory() === $this) {
                $request->setCategory(null);
            }
        }

        return $this;
    }
}
