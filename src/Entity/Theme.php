<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Les Thèmes sont, dans les catégories un emplacement sur un sujet précis. Dans un thème on peut ensuite créer des
 * ateliers. Les thèmes ont un nom, une image, une description et permettent ou non les délégations ainsi que leur
 * profondeur. Ils peuvent être publics (visibles aux utilisateurs non connectés ou non souscrits à la catégorie) ou
 * non.
 * @ORM\Entity(repositoryClass="App\Repository\ThemeRepository")
 * @UniqueEntity(fields={"name"}, message="name.alreadyexists")
 * @Vich\Uploadable
 */
class Theme
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var L'identifiant dans la BDD
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank
     * @var string Nom du thème
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string Chemin de l'image dans l'arborescence du site
     */
    private $image;
    /**
     * @Vich\UploadableField(mapping="themes_images", fileNameProperty="image")
     * @var File L'image elle-même
     */
    private $imageFile;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime Le moment du téléversage de l'image
     */
    private $updatedAt;
    /**
     * @ORM\Column(type="string", length=1048576)
     * @Assert\NotBlank
     * @Assert\Length(
     *    min = 6,
     *    minMessage = "length.min.6",
     *    max = 1048576,
     *    maxMessage = "length.max.1048576"
     * )
     * @var string La description du thème. TODO: Le sauver/écrire en markdown. A l'heure actuelle avec CKEditor en HTML
     */
    private $description;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Workshop", mappedBy="theme", orphanRemoval=true)
     * @var Collection|Workshop[]  Les ateliers contenus dans ce thème
     */
    private $workshops;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Delegation", mappedBy="theme", orphanRemoval=true)
     * @var Collection|Delegation[] Les délégations concernant ce thème
     */
    private $delegations;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="themes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Category La catégorie dans laquelle le thème est contenue. Si aucune n'est choisie il sera dans la
     * catégorie Défaut
     */
    private $category;

    /**
     * @ORM\Column(type="boolean")
     * @var bool Définit si le thème est public. Affecte la visibilité du thème auprès des utilisateurs souscrits ou
     * non connectés
     */
    private $isPublic;



    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int Définit la profondeur de la délégation si elle est autorisée. 0 = infini, 1 = Une seule délégation,
     * etc..
     */
    private $delegationDeepness;

    /**
     * @ORM\Column(type="string")
     * @var string Autorise ou non la délégations des votes sur ce thème et l'ensemble de ses ateliers
     * 4 choix possibles :
     * - Vote a 3 niveaux sans délégation : no-delegation
     * - Vote a 3 niveaux avec délégation : yes-delegation
     * - Vote avec poids                  : weighted
     * - Vote a 5 niveaux sans délégation : levelled
     */
    private $voteType;

    /**
     * @return string
     */
    public function getVoteType(): string
    {
        return $this->voteType;
    }

    /**
     * @param string $voteType
     */
    public function setVoteType(string $voteType): void
    {
        $this->voteType = $voteType;
    }



    public function __construct()
    {
        $this->voteType = 'no-delegation';
        $this->workshops = new ArrayCollection();
        $this->delegations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
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

    /**
     * @return Collection|Workshops[]
     */
    public function getWorkshops(): Collection
    {
        return $this->workshops;
    }

    public function addWorkshop(Workshop $workshop): self
    {
        if (!$this->workshops->contains($workshop)) {
            $this->workshops[] = $workshop;
            $workshop->setTheme($this);
        }

        return $this;
    }

    public function removeWorkshop(Workshop $workshop): self
    {
        if ($this->workshops->contains($workshop)) {
            $this->workshops->removeElement($workshop);
            // set the owning side to null (unless already changed)
            if ($workshop->getTheme() === $this) {
                $workshop->setTheme(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Delegation[]
     */
    public function getDelegations(): Collection
    {
        return $this->delegations;
    }

    public function addDelegation(Delegation $delegation): self
    {
        if (!$this->delegations->contains($delegation)) {
            $this->delegations[] = $delegation;
            $delegation->setTheme($this);
        }

        return $this;
    }

    public function removeDelegation(Delegation $delegation): self
    {
        if ($this->delegations->contains($delegation)) {
            $this->delegations->removeElement($delegation);
            // set the owning side to null (unless already changed)
            if ($delegation->getTheme() === $this) {
                $delegation->setTheme(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }



    public function getDelegationDeepness(): ?int
    {
        return $this->delegationDeepness;
    }

    public function setDelegationDeepness(?int $delegationDeepness): self
    {
        $this->delegationDeepness = $delegationDeepness;

        return $this;
    }


}