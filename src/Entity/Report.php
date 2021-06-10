<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Les signalements sont crées quand un moderateur signale un forum.
 * @ORM\Entity(repositoryClass=ReportRepository::class)
 */
class Report
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int L'identifiant dans la BDD
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTimeInterface La date du signalement
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reports",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @var User L'utilisateur concerné par le signalement
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Forum::class, inversedBy="reports",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @var Forum Le forum entrainant le signalement
     */
    private $forum;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getForum(): ?Forum
    {
        return $this->forum;
    }

    public function setForum(?Forum $forum): self
    {
        $this->forum = $forum;

        return $this;
    }
}
