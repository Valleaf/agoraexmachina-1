<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Les documents sont des fichiers téléversés sur la BDD et accessibles depuis le site sur la page d'un atelier. Ils
 * sont actuellement limités en .pdf et à une certaine taille. Cela est modifiable dans DocumentType.
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @Vich\Uploadable()
 */
class Document
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
     * @var string Nom du document
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string Chemin du fichier dans le dossier du site
     */
    private $path;

    /**
     * @Vich\UploadableField(mapping="workshops_documents", fileNameProperty="path")
     * @var File le fichier téléversé
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity=Workshop::class, inversedBy="documents",cascade={"persist","remove"})
     * @ORM\JoinColumn(nullable=false)
     * @var Workshop L'atelier associé
     */
    private $workshop;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime La date où le fichier fut téléversé
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }


    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path = null): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }


    public function setFile(File $file): void
    {
        $this->file = $file;
        if($file)
        {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getWorkshop(): ?Workshop
    {
        return $this->workshop;
    }

    public function setWorkshop(?Workshop $workshop): self
    {
        $this->workshop = $workshop;

        return $this;
    }

}
