<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Les ateliers font parti d'un thème et d'une catégorie dépendante de ce thème. Ils peuvent contenir des
 * propositions et à l'heure actuelle les paramètres de délégations dépendent du thème. Ils ont aussi un interval de
 * temps pour discuter et un autre pour voter. Ils peuvent également avoir des documents (pdf) associés. Ils peuvent
 * avoir des mots-clés pour faciliter une traversée horizontale de l'application par les utilisateurs.
 * @ORM\Entity(repositoryClass="App\Repository\WorkshopRepository")
 * @Vich\Uploadable
 */
class Workshop
{

	public function __construct()
                                                                                    	{
                                                                                    		$d1						 = new \DateTime();
                                                                                    		$this->dateBegin		 = $d1;
                                                                                    		$this->dateVoteBegin     = $d1;
                                                                                    		$d2						 = new \DateTime();
                                                                                    		$this->dateEnd			 = $d2->modify('+1 month');
                                                                                    		$this->dateVoteEnd	     = $d2->modify('+1 month');
                                                                                    		$this->quorumRequired	 = 0;
                                                                                    		$this->rightsDelegation	 = true;
                                                                                    		$this->proposals		 = new ArrayCollection();
                                                                                    		$this->delegations		 = new ArrayCollection();
                                                                                    		$this->documents         = new ArrayCollection();
                                                                                          $this->keywords = new ArrayCollection();
                                                                                          $this->quorumRequired=51;
                                                                                    	}

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
     * @var int L'identifiant dans la BDD
	 */
	private $id;
	/**
	 * @ORM\Column(type="string", length=50)
     * @var string Nom de l'atelier
	 */
	private $name;
	/**
     * @ORM\Column(type="string", length=1048576)
     * @Assert\NotBlank
     * @Assert\Length(
     *    min = 6,
     *    minMessage = "length.min.6",
     *    max = 1048576,
     *    maxMessage = "length.max.1048576"
     * )     * @var string Description du sujet de l'atelier
	 */
	private $description;
	/**
	 * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual(
     *      value = "today",
     *      message = "date.not.today"
     * )
     * @var \DateTimeInterface Le début de la discussion sur l'atelier
	 */
	private $dateBegin;
	/**
	 * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual(
     *      value = "today",
     *      message = "date.not.today"
     * )
     * @Assert\Expression(
     *     "this.getDateEnd() >= this.getDateBegin()",
     *     message="date.more.than"
     * )
     * @var \DateTimeInterface La fin de la discussion sur l'atelier
	 */
	private $dateEnd;
	/**
	 * @ORM\Column(type="string", length=1024)
     * @var string Actuellement disponible sur "Everyone"
	 */
	private $rightsSeeWorkshop;
	/**
	 * @ORM\Column(type="string", length=1024)
     * @var string Actuellement disponible sur "Everyone"
	 */
	private $rightsVoteProposals;
	/**
	 * @ORM\Column(type="string", length=1024)
     * @var string Actuellement disponible sur "Everyone"
	 */
	private $rightsWriteProposals;

	/**
	 * @ORM\Column(type="integer", nullable=true)
     * @var integer En pourcent. Non utilisé à l'heure actuelle (?)
	 */
	private $quorumRequired;
	/**
	 * @ORM\Column(type="boolean")
     * @var bool A l'heure actuelle délégations uniquement sur les thèmes. Non utilisé donc
	 */
	private $rightsDelegation;
	/**
	 * @ORM\ManyToOne(targetEntity="Theme", inversedBy="workshops")
	 * @ORM\JoinColumn(nullable=false)
     * @var Theme Le thème dont l'atelier dépend
	 */
	private $theme;
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 * @var string Le chemin de l'image dans l'atelier
	 */
	private $image;
	/**
	 * @Vich\UploadableField(mapping="workshops_images", fileNameProperty="image")
	 * @var File L'image de l'atelier
	 */
	private $imageFile;
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @var \DateTime Date du téléversage de l'image
	 */
	private $updatedAt;
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Proposal", mappedBy="workshop", orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Collection|Proposal[] Les propositions associés à l'atelier
	 */
	private $proposals;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Delegation", mappedBy="workshop")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Collection|Delegation[] Actuellement non utilisé. Délégations sur les thèmes.
	 */
	private $delegations;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="workshops")
     * @ORM\JoinColumn(nullable=false)
     * @var User L'utilisateur ayant crée l'atelier
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="workshop", orphanRemoval=true,cascade={"persist","remove"})
     * @var Collection|Document[] Les documents associés à l'atelier. Actuellement des pdf.
     */
    private $documents;



    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual(
     *      value = "today",
     *      message = "date.not.today"
     * )
     * @var \DateTimeInterface Le début du vote
     */
    private $dateVoteBegin;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual(
     *      value = "today",
     *      message = "date.not.today"
     * )
     * @Assert\Expression(
     *     "this.getDateVoteEnd() >= this.getDateVoteBegin()",
     *     message="date.more.than"
     * )
     * @var \DateTimeInterface Fin du vote
     */
    private $dateVoteEnd;

    /**
     * @ORM\ManyToMany(targetEntity=Keyword::class, inversedBy="workshops",cascade={"persist"})
     * @var Collection|Keyword[] Les mots-clés associés à cet atelier
     */
    private $keywords;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string Utilisé pour faciliter l'ajout de mots-clés à la BDD
     */
    private $keytext;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var Collection|Delegation[] Actuellement non utilisé. Délégations sur les thèmes.
     */
    private $delegationDeepness;

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

	public function getDateBegin(): ?\DateTimeInterface
                                                                                    	{
                                                                                    		return $this->dateBegin;
                                                                                    	}

	public function setDateBegin(\DateTimeInterface $dateBegin): self
                                                                                    	{
                                                                                    		$this->dateBegin = $dateBegin;
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function getDateEnd(): ?\DateTimeInterface
                                                                                    	{
                                                                                    		return $this->dateEnd;
                                                                                    	}

	public function setDateEnd(\DateTimeInterface $dateEnd): self
                                                                                    	{
                                                                                    		$this->dateEnd = $dateEnd;
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function getRightsSeeWorkshop(): ?string
                                                                                    	{
                                                                                    		return $this->rightsSeeWorkshop;
                                                                                    	}

	public function setRightsSeeWorkshop(string $rightsSeeWorkshop): self
                                                                                    	{
                                                                                    		$this->rightsSeeWorkshop = $rightsSeeWorkshop;
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function getRightsVoteProposals(): ?string
                                                                                    	{
                                                                                    		return $this->rightsVoteProposals;
                                                                                    	}

	public function setRightsVoteProposals(string $rightsVoteProposals): self
                                                                                    	{
                                                                                    		$this->rightsVoteProposals = $rightsVoteProposals;
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function getRightsWriteProposals(): ?string
                                                                                    	{
                                                                                    		return $this->rightsWriteProposals;
                                                                                    	}

	public function setRightsWriteProposals(string $rightsWriteProposals): self
                                                                                    	{
                                                                                    		$this->rightsWriteProposals = $rightsWriteProposals;
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function getQuorumRequired(): ?int
                                                                                    	{
                                                                                    		return $this->quorumRequired;
                                                                                    	}

	public function setQuorumRequired(?int $quorumRequired): self
                                                                                    	{
                                                                                    		$this->quorumRequired = $quorumRequired;
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function getRightsDelegation(): ?bool
                                                                                    	{
                                                                                    		return $this->rightsDelegation;
                                                                                    	}

	public function setRightsDelegation(bool $rightsDelegation): self
                                                                                    	{
                                                                                    		$this->rightsDelegation = $rightsDelegation;
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function getTheme(): ?Theme
                                                                                    	{
                                                                                    		return $this->theme;
                                                                                    	}

	public function setTheme(?Theme $theme): self
                                                                                    	{
                                                                                    		$this->theme = $theme;
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function setImageFile(File $image = null)
                                                                                    	{
                                                                                    		$this->imageFile = $image;
                                                                                    
                                                                                    		// VERY IMPORTANT:
                                                                                    		// It is required that at least one field changes if you are using Doctrine,
                                                                                    		// otherwise the event listeners won't be called and the file is lost
                                                                                    		if($image)
                                                                                    		{
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
	 * @return Collection|Proposals[]
	 */
	public function getProposals(): Collection
                                                                                    	{
                                                                                    		return $this->proposals;
                                                                                    	}

	public function addProposal(Proposal $proposal): self
                                                                                    	{
                                                                                    		if( ! $this->Proposals->contains($proposal))
                                                                                    		{
                                                                                    			$this->Proposals[] = $proposal;
                                                                                    			$proposal->setWorkshop($this);
                                                                                    		}
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function removeProposal(Proposal $proposal): self
                                                                                    	{
                                                                                    		if($this->Proposals->contains($proposal))
                                                                                    		{
                                                                                    			$this->Proposals->removeElement($proposal);
                                                                                    			// set the owning side to null (unless already changed)
                                                                                    			if($proposal->getWorkshop() === $this)
                                                                                    			{
                                                                                    				$proposal->setWorkshop(null);
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
                                                                                    		if( ! $this->delegations->contains($delegation))
                                                                                    		{
                                                                                    			$this->delegations[] = $delegation;
                                                                                    			$delegation->setWorkshop($this);
                                                                                    		}
                                                                                    
                                                                                    		return $this;
                                                                                    	}

	public function removeDelegation(Delegation $delegation): self
                                                                                    	{
                                                                                    		if($this->delegations->contains($delegation))
                                                                                    		{
                                                                                    			$this->delegations->removeElement($delegation);
                                                                                    			// set the owning side to null (unless already changed)
                                                                                    			if($delegation->getWorkshop() === $this)
                                                                                    			{
                                                                                    				$delegation->setWorkshop(null);
                                                                                    			}
                                                                                    		}
                                                                                    
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

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setWorkshop($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getWorkshop() === $this) {
                $document->setWorkshop(null);
            }
        }

        return $this;
    }



    public function getDateVoteBegin(): ?\DateTimeInterface
    {
        return $this->dateVoteBegin;
    }

    public function setDateVoteBegin(\DateTimeInterface $dateVoteBegin): self
    {
        $this->dateVoteBegin = $dateVoteBegin;

        return $this;
    }

    public function getDateVoteEnd(): ?\DateTimeInterface
    {
        return $this->dateVoteEnd;
    }

    public function setDateVoteEnd(\DateTimeInterface $dateVoteEnd): self
    {
        $this->dateVoteEnd = $dateVoteEnd;

        return $this;
    }

    /**
     * @return Collection|Keyword[]
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function addKeyword(Keyword $keyword): self
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords[] = $keyword;
        }

        return $this;
    }

    public function removeKeyword(Keyword $keyword): self
    {
        $this->keywords->removeElement($keyword);

        return $this;
    }

    public function getKeytext(): ?string
    {
        return $this->keytext;
    }

    public function setKeytext(?string $keytext): self
    {
        $this->keytext = $keytext;

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

    /**
     * @return bool Retourne vrai si le vote est ouvert.
     */
    public function voteStatus()
    {
        # Verification si la date de debut est dans le passé
        $isStarted = date_diff(new \DateTime('now'),$this->getDateVoteBegin());
        # On convertit la date en +/- et le nombres de jours, en entier
        $isStarted = (int)$isStarted->format("%R%a");
        if($isStarted > 0)
        {
            return false;
        }

        # Verification si la date de fin est dans le passé
        $isOver = date_diff(new \DateTime('now'),$this->getDateVoteEnd());
        # On convertit la date en +/- et le nombres de jours, en entier
        $isOver = (int)$isOver->format("%R%a");
        if($isOver < 0)
        {
            return false;
        }
        return true;

    }

    /**
     * @return bool Retourne vrai si la discussion est ouverte.
     */
    public function forumStatus()
    {
        # Verification si la date de debut est dans le passé
        $isStarted = date_diff(new \DateTime('now'),$this->getDateBegin());
        # On convertit la date en +/- et le nombres de jours, en entier
        $isStarted = (int)$isStarted->format("%R%a");
        if($isStarted >= 0)
        {
            return false;
        }

        # Verification si la date de fin est dans le passé
        $isOver = date_diff(new \DateTime('now'),$this->getDateEnd());
        # On convertit la date en +/- et le nombres de jours, en entier
        $isOver = (int)$isOver->format("%R%a");
        if($isOver < 0)
        {
            return false;
        }
        return true;

    }
}