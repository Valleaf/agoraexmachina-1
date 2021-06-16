<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Les propositions sont dans les ateliers et autorisent en leur sein des forums et des votes.
 * @ORM\Entity(repositoryClass="App\Repository\ProposalRepository")
 */
class Proposal
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
     * @var int L'identifiant dans la BDD
	 */
	private $id;
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Workshop", inversedBy="proposals")
	 * @ORM\JoinColumn(nullable=false)
     * @var Workshop L'atelier attaché à la proposition
	 */
	private $workshop;
	/**
	 * @ORM\Column(type="string", length=255)
     * @var string Le nom de la proposition
	 */
	private $name;
	/**
	 * @ORM\Column(type="text")
     * @Assert\NotNull()
     * @var string La description de la proposition
	 */
	private $description;
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Forum", mappedBy="proposal", orphanRemoval=true)
     * @var Collection|Forum[] Les forums attachés à la proposition
	 */
	private $forums;
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="proposals")
	 * @ORM\JoinColumn(nullable=false)
     * @var User L'utilisateur ayant crée la proposition
	 */
	private $user;
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="proposal", orphanRemoval=true)
     * @var Collection|Vote[] Les votes dans la proposition
	 */
	private $votes;

    public function __toString()
    {
        return $this->getName();
    }


    public function __construct()
	{
		$this->votes	 = new ArrayCollection();
		$this->forums	 = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
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

	public function getUser(): ?user
	{
		return $this->user;
	}

	public function setUser(?user $user): self
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * @return Collection|Votes[]
	 */
	public function getVotes(): Collection
	{
		return $this->votes;
	}

	public function addVote(Vote $vote): self
	{
		if( ! $this->votes->contains($vote))
		{
			$this->votes[] = $vote;
			$vote->setProposal($this);
		}

		return $this;
	}

	public function removeVote(Vote $vote): self
	{
		if($this->votes->contains($vote))
		{
			$this->votes->removeElement($vote);
			// set the owning side to null (unless already changed)
			if($vote->getProposal() === $this)
			{
				$vote->setProposal(null);
			}
		}

		return $this;
	}
	
	
	/**
	 * @return Collection|Forum[]
	 */
	public function getForums(): Collection
	{
		return $this->forums;
	}

	public function addForum(Forum $forum): self
	{
		if( ! $this->forums->contains($forum))
		{
			$this->forums[] = $forum;
			$forum->setProposal($this);
		}

		return $this;
	}

	public function removeForum(Forum $forum): self
	{
		if($this->forums->contains($forum))
		{
			$this->forums->removeElement($forum);
			// set the owning side to null (unless already changed)
			if($forum->getProposal() === $this)
			{
				$forum->setProposal(null);
			}
		}

		return $this;
	}

}