<?php

namespace App\Entity;

use App\Repository\WebsiteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * L'entité gérant quelques paramètres, actuellement sert principalement pour l'affichage. On n'utilise qu'une seule
 * entité qui est crée lors du setup du site
 * @ORM\Entity(repositoryClass=WebsiteRepository::class)
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
}
