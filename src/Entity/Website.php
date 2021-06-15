<?php

namespace App\Entity;

use App\Repository\WebsiteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * L'entité gérant quelques paramètres, actuellement sert principalement pour l'affichage. On n'utilise qu'une seule
 * entité qui est crée lors du setup du site
 * @ORM\Entity(repositoryClass=WebsiteRepository::class)
 * @Vich\Uploadable
 */
class Website
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int L'identifiant dans la BDD. Il n'y aura qu'un site avec un identifiant 1
     */
    private $id;

    /**
     * Website constructor. Les données par défaut pour le site
     */
    public function __construct()
    {
        $this->id=1;
        $this->title="AGORA Ex Machina";
        $this->version="v0.9.2";
        $this->name="CRLBazin";
        $this->email="crlbazin@gmail.com";
    }


    /**
     * @ORM\Column(type="string", length=255)
     * @var string Titre du site, affiché dans les onglets et en haut à gauche dans la navigation
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string Version du site
     */
    private $version;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string Nom de l'auteur du site
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string Email de l'administrateur
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $registrationMessage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $loginMessage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundColor;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string Chemin de l'image dans l'arborescence du site
     */
    private $image;
    /**
     * @Vich\UploadableField(mapping="wallpapers_images", fileNameProperty="image")
     * @var File L'image elle-même
     */
    private $imageFile;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime Le moment du téléversage de l'image
     */
    private $updatedAt;

    #TODO: Ajouter l'opacité

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRegistrationMessage(): ?string
    {
        return $this->registrationMessage;
    }

    public function setRegistrationMessage(?string $registrationMessage): self
    {
        $this->registrationMessage = $registrationMessage;

        return $this;
    }

    public function getLoginMessage(): ?string
    {
        return $this->loginMessage;
    }

    public function setLoginMessage(?string $loginMessage): self
    {
        $this->loginMessage = $loginMessage;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(?string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

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
}
