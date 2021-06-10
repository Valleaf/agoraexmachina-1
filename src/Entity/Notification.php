<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Les notifications permettent une communication indirecte entre utilisateurs. Elles permettent de notifier un
 * utilisateur lorsqu'un événement potentiellement intéressant est accessible. C'est aussi, associé avec une Requête
 * un moyen d'ajouter un utilisateur à une catégorie
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int L'identifiant dans la BDD
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @var \DateTimeInterface la date de la notification
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=1048576)
     * @var string Le sujet de la notification
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     * @var User L'utilisateur recevant la notification
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     * @var bool Le statut de la notificaiton, permet de voir si elle est lue ou non. Est non-lue par défaut puis
     * passe lue quand la page des notifications est ouverte
     */
    private $isRead;

    /**
     * @ORM\ManyToOne(targetEntity=Request::class, inversedBy="notifications",cascade={"persist"})
     * @var Request Une requête peut être attachée à une notification, pour faire une demande de rejoindre une
     * catégorie par un utilisateur.
     */
    private $request;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
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

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }


    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function setRequest(?Request $request): self
    {
        $this->request = $request;

        return $this;
    }
}
