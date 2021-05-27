<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DelegationRepository")
 */
class Delegation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="delegationsFrom")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="delegationsTo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userTo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Workshop", inversedBy="delegations")
     */
    private $workshop;

    /**
     * @ORM\ManyToOne(targetEntity="Theme", inversedBy="delegations")
     */
    private $theme;

    /**
     * @ORM\Column(type="integer", nullable=true)
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
