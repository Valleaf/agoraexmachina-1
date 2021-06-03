<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Les délégations permettent à un utilisateur de donner son vote à un autre utilisateur. Les conditions dépendent
 * des thèmes. Sont ainsi définissables la profondeur possible et l'autorisation ou non des délégations.
 * @ORM\Entity(repositoryClass="App\Repository\DelegationRepository")
 */
class Delegation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int L'identifiant dans la BDD
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="delegationsFrom")
     * @ORM\JoinColumn(nullable=false)
     * @var User L'utilisateur envoyant la délégation
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="delegationsTo")
     * @ORM\JoinColumn(nullable=false)
     * @var User L'utilisateur recevant la délégation
     */
    private $userTo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Workshop", inversedBy="delegations")
     * @var Workshop L'atelier concerné par la délégation
     */
    private $workshop;

    /**
     * @ORM\ManyToOne(targetEntity="Theme", inversedBy="delegations")
     * @var Theme Le thème concerné par la délégation
     */
    private $theme;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int La profondeur actuelle de la délégation. (Est définie à 1 lors du la première délégation d'un vote,
     * et est incrémentée un à un pour chaque transmission supplémentaire)
     */
    private $deepness;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserFrom(): ?user
    {
        return $this->userFrom;
    }

    public function setUserFrom(?user $userFrom): self
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    public function getUserTo(): ?User
    {
        return $this->userTo;
    }

    public function setUserTo(?User $userTo): self
    {
        $this->userTo = $userTo;

        return $this;
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

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getDeepness(): ?int
    {
        return $this->deepness;
    }

    public function setDeepness(?int $deepness): self
    {
        $this->deepness = $deepness;

        return $this;
    }
}
