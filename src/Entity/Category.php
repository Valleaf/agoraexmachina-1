<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Les catégories permettent de ranger les thèmes et ateliers. Les utilisateurs peuvent être
 * souscrits à des catégories pour accéder à leur contenu. Les modérateurs et administrateurs restreints peuvent être
 * inscrits à des catégories pour pouvoir opérer dans ces catégories
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @UniqueEntity(fields={"name"}, message="name.alreadyexists")
 * @Vich\Uploadable
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
     * @ORM\OneToMany(targetEntity=Theme::class, mappedBy="category",cascade={"remove","persist"})
     * @var Collection|Theme[] Les thèmes associés à cette catégorie
     */
    private $themes;


    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="categories",cascade={"persist"})
     * @var Collection|User[] Les utilisateurs souscrits à cette catégorie
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Request::class, mappedBy="category", orphanRemoval=true,cascade={"persist"})
     * @var Collection|Request[] Les requêtes associés à une demande pour rejoindre cette catégorie
     */
    private $requests;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string Chemin de l'image dans l'arborescence du site
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="categories_images", fileNameProperty="image")
     * @var File L'image elle-même
     */
    private $imageFile;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime Le moment du téléversage de l'image
     */
    private $updatedAt;


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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function __serialize()
    {
        return [
            $this->imageFile = base64_encode($this->imageFile)
        ];
    }

    public function __unserialize($serialized)
    {
        $this->imageFile = base64_decode($this->imageFile);
    }
}
