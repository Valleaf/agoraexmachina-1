<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Les forums permettent à un utilisateur de lancer une discussion dans une proposition. Ils peuvent aussi être en
 * réponse à un autre forum. La profondeur maximum de la réponse est actuellement de 1.
 * @ORM\Entity(repositoryClass="App\Repository\ForumRepository")
 */
class Forum
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int L'identifiant dans la BDD
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string Le titre du forum
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @var string Le texte contenu dans le post
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="forums")
     * @ORM\JoinColumn(nullable=false)
     * @var L'utilisateur ayant crée le forum
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Proposal", inversedBy="forums")
     * @ORM\JoinColumn(nullable=false)
     * @var Proposal La proposition à laquelle le forum est rattaché
     */
    private $proposal;

    /**
     * @ORM\ManyToOne(targetEntity=Forum::class, inversedBy="forums",cascade={"remove"})
     * @var Forum Si le forum est en réponse à un forum, c'est dans cette variable qu'il se trouve
     */
    private $parentForum;

    /**
     * @ORM\OneToMany(targetEntity=Forum::class, mappedBy="parentForum",orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Collection|Forum[] Si le forum à des réponses, ils se trouveront ici
     */
    private $forums;

    /**
     * @ORM\OneToMany(targetEntity=Report::class, mappedBy="forum", orphanRemoval=true)
     */
    private $reports;

    public function __construct()
    {
        $this->forums = new ArrayCollection();
        $this->reports = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProposal(): ?Proposal
    {
        return $this->proposal;
    }

    public function setProposal(?Proposal $proposal): self
    {
        $this->proposal = $proposal;

        return $this;
    }

    public function getParentForum(): ?self
    {
        return $this->parentForum;
    }

    public function setParentForum(?self $parentForum): self
    {
        $this->parentForum = $parentForum;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getForums(): Collection
    {
        return $this->forums;
    }

    public function addForum(self $forum): self
    {
        if (!$this->forums->contains($forum)) {
            $this->forums[] = $forum;
            $forum->setParentForum($this);
        }

        return $this;
    }

    public function removeForum(self $forum): self
    {
        if ($this->forums->contains($forum)) {
            // set the owning side to null (unless already changed)
            if ($forum->getParentForum() === $this) {
                $forum->setParentForum(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setForum($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getForum() === $this) {
                $report->setForum(null);
            }
        }

        return $this;
    }

    /**
     * @return Report Retourne un signalement pret a etre envoye a la BDD
     */
    public function createReport(): Report
    {
        $report = new Report;
        $report->setUser($this->getUser());
        $report->setDate(new \DateTime('now'));
        $report->setForum($this);
        return $report;

    }
}
