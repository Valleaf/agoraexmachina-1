<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Les requêtes sont combinées à des notifications. Elles représentent une demande pour rejoindre une catégorie.
 * @ORM\Entity(repositoryClass=RequestRepository::class)
 */
class Request
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int L'identifiant dans la BDD
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="requests")
     * @ORM\JoinColumn(nullable=false)
     * @var User L'utilisateur envoyant la requête
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="requests")
     * @ORM\JoinColumn(nullable=false)
     * @var Category La catégorie concernée par la requête
     */
    private $category;

    /**
     * @ORM\Column(type="boolean")
     * @var bool Avant le verdict, faux. Après acceptation ou refus devient vrai
     */
    private $isDone;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="request",cascade={"persist", "remove"})
     * @var Collection|Notification[] Une requête peut être attachée à plusieurs notifications
     */
    private $notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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



    public function getIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setRequest($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getRequest() === $this) {
                $notification->setRequest(null);
            }
        }

        return $this;
    }
}
