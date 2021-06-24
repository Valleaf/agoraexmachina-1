<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * C'est la classe utilisateur de l'application. Les utilisateurs ont un Prénom, nom, pseudo, adresse email, mot de
 * passe. TODO: Ils peuvent aussi avoir un avatar (WIP)
 * Les utilisateurs peuvent demander à rejoindre des catégories. Une fois acceptés, ils peuvent y soumettre des
 * ateliers, des propositions, des forums et voter. Ils peuvent aussi déléguer leurs vote et délégations reçues selon
 * les conditions des thèmes.
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="username.alreadyexists")
 * @UniqueEntity(fields={"email"}, message="email.alreadyexists")
 * @Vich\Uploadable
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int L'identifiant dans la BDD
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     * @var string Le pseudo de l'utilisateur
     */
    private $username;

    /**
     * @return string Retourne le pseudo de l'utilisateur. Utilisé pour l'affichage
     */
    public function __toString()
    {
        return $this->username;
    }


    /**
     * @ORM\Column(type="json")
     * @var Collection Les rôles disponibles sont ROLE_ADMIN, ROLE_ADMIN_RESTRICTED, ROLE_MODERATOR, ROLE_USER avec
     * hiérarchie des rôles, ainsi un admin sera aussi considéré comme restreint, modérateur et user, etc..
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @var string Mot de passe encrypté
     */
    private $password;

    /**
     * @Assert\Regex(
     * pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{10,}$/",
     * match=true)
     * @var string Non gardé dans la BDD, sert lors d'un changement de mot de passe. Il est ensuite encodé pour
     * remplir la colonne password
     */
    private $plainPassword;

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
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
     * @ORM\OneToMany(targetEntity="App\Entity\Proposal", mappedBy="user")
     * @var Collection|Proposal[] Les propositions créées par l'utilisateur
     */
    private $proposals;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote",mappedBy="user", orphanRemoval=true)
     * @var Collection|Vote[] Les votes effectués par l'utilisateur
     */
    private $votes;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(
     *    message = "email.notvalid"
     * )
     * @var string L'email de l'utilisateur. Sert à se connecter avec le password
     */
    private $email;
#TODO: FIX ALL THE CASCADE
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Forum", mappedBy="user", orphanRemoval=true)
     * @var Collection|Forum[] Les forums crées par l'utilisateur
     */
    private $forums;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Delegation", mappedBy="userFrom", orphanRemoval=true)
     * @var Collection|Delegation[] Les délégations reçues par l'utilisateur
     */
    private $delegationsFrom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Delegation", mappedBy="userTo", orphanRemoval=true)
     * @var Collection|Delegation[] Les délégations envoyées par l'utilisateur
     */
    private $delegationsTo;

    /**
     * @ORM\OneToMany(targetEntity=Workshop::class, mappedBy="user",orphanRemoval=true)
     * @var Collection|Workshop[] Les ateliers crées par cet utilisateur
     */
    private $workshops;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="users",cascade={"all"})
     * @var Collection|Category[] Les catégories suivies par l'utilisateur
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user", orphanRemoval=true)
     * @var Collection|Notification[] Les notifications reçues par l'utilisateur
     */
    private $notifications;

    /**
     * @ORM\Column(type="boolean")
     * @var bool Si vrai, l'envoi des emails à cet utilisateur est possible
     */
    private $isAllowedEmails;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string Le chemin de l'image dans l'arborescence
     */
    private $image;
    /**
     * @Vich\UploadableField(mapping="users_images", fileNameProperty="image")
     * @var File L'avatar de l'utilisateur
     */
    private $imageFile;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime Date du téléversement de l'avatar
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=40)
     * @var string Prénom de l'utilisateur
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=40)
     * @var string Le nom de famille de l'utilisateur
     */
    private $lastName;

    /**
     * @ORM\OneToMany(targetEntity=Request::class, mappedBy="user", orphanRemoval=true)
     * @var Collection|Request[] Les requêtes pour catégorie faites par l'utilisateur
     */
    private $requests;

    /**
     * @ORM\OneToMany(targetEntity=Report::class, mappedBy="user", orphanRemoval=true)
     */
    private $reports;

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }


    public function __construct()
    {
        $this->proposals = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->forums = new ArrayCollection();
        $this->delegationsFrom = new ArrayCollection();
        $this->delegationsTo = new ArrayCollection();
        $this->workshops = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->requests = new ArrayCollection();
        $this->reports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
        if (!$this->proposals->contains($proposal)) {
            $this->proposals[] = $proposal;
            $proposal->setUser($this);
        }

        return $this;
    }

    public function removeProposal(Proposal $proposal): self
    {
        if ($this->proposals->contains($proposal)) {
            $this->proposals->removeElement($proposal);
            // set the owning side to null (unless already changed)
            if ($proposal->getUser() === $this) {
                $proposal->setUser(null);
            }
        }

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
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setUser($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getUser() === $this) {
                $vote->setUser(null);
            }
        }

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

    /**
     * @return Collection|Forums[]
     */
    public function getForums(): Collection
    {
        return $this->forums;
    }

    public function addForum(Forum $forum): self
    {
        if (!$this->forums->contains($forum)) {
            $this->forums[] = $forum;
            $forum->setUser($this);
        }

        return $this;
    }

    public function removeForum(Forum $forum): self
    {
        if ($this->forums->contains($forum)) {
            $this->forums->removeElement($forum);
            // set the owning side to null (unless already changed)
            if ($forum->getUser() === $this) {
                $forum->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DelegationFrom[]
     */
    public function getDelegationsFrom(): Collection
    {
        return $this->delegationsFrom;
    }

    /**
     * @return Collection|DelegationTo[]
     */
    public function getDelegationsTo(): Collection
    {
        return $this->delegationsTo;
    }

    public function addDelegationFrom(Delegation $delegationFrom): self
    {
        if (!$this->delegationsFrom->contains($delegationFrom)) {
            $this->delegationsFrom[] = $delegationFrom;
            $delegationFrom->setUserFrom($this);
        }

        return $this;
    }

    public function addDelegationTo(Delegation $delegationTo): self
    {
        if (!$this->delegationsTo->contains($delegationTo)) {
            $this->delegationsTo[] = $delegationTo;
            $delegationTo->setUserFrom($this);
        }

        return $this;
    }

    public function removeDelegationFrom(Delegation $delegationFrom): self
    {
        if ($this->delegationsFrom->contains($delegationFrom)) {
            $this->delegationsFrom->removeElement($delegationFrom);
            // set the owning side to null (unless already changed)
            if ($delegationFrom->getUserFrom() === $this) {
                $delegationFrom->setUserFrom(null);
            }
        }

        return $this;
    }


    public function removeDelegationTo(Delegation $delegationTo): self
    {
        if ($this->delegationsTo->contains($delegationTo)) {
            $this->delegationsTo->removeElement($delegationTo);
            // set the owning side to null (unless already changed)
            if ($delegationTo->getUserFrom() === $this) {
                $delegationTo->setUserFrom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Workshop[]
     */
    public function getWorkshops(): Collection
    {
        return $this->workshops;
    }

    public function addWorkshop(Workshop $workshop): self
    {
        if (!$this->workshops->contains($workshop)) {
            $this->workshops[] = $workshop;
            $workshop->setUser($this);
        }

        return $this;
    }

    public function removeWorkshop(Workshop $workshop): self
    {
        if ($this->workshops->contains($workshop)) {
            $this->workshops->removeElement($workshop);
            // set the owning side to null (unless already changed)
            if ($workshop->getUser() === $this) {
                $workshop->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

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
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getIsAllowedEmails(): ?bool
    {
        return $this->isAllowedEmails;
    }

    public function setIsAllowedEmails(bool $isAllowedEmails): self
    {
        $this->isAllowedEmails = $isAllowedEmails;

        return $this;
    }

    /**
     * @param $subject string Sujet de la notification et son texte
     * @return Notification Une notification prête à être envoyée à l'utilisateur
     */
    public function prepareNotification(string $subject): Notification
    {
        $notification = new Notification();
        $notification
            ->setDate(new \DateTime('now'))
            ->setIsRead(false)
            ->setSubject($subject)
            ->setUser($this);
        return $notification;
    }

    /**
     * Cette fonction permet d'envoyer un courriel a l'utilisateur
     * @param MailerInterface $mailer
     * @param string $subject Le sujet du courriel
     * @param string $body Le corps du courriel
     */
    public function sendEmailToUser(MailerInterface $mailer,$sender, string $subject, string $body): void
    {
        $pattern = '/:\/\//';
        $sender = preg_split($pattern,$sender)[1];
        $pattern = '/:/';
        $sender = preg_split($pattern,$sender)[0];


        $email = (new Email())
            ->from($sender)
            ->to($this->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject($subject)
            ->text($body);
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface  $e)
        {

        }
    }

    /**
     * @return int Retourne le nombre de notifications non lues de l'utilisateur
     */
    public function numberUnreadNotifications(): int
    {
        $count = 0;
        $notifications = $this->getNotifications();
        foreach ($notifications as $notification) {
            if (!$notification->getIsRead()) {
                $count++;
            }
        }
        return $count;
    }

        public function __serialize(): array
        {
            return [
                'id' => $this->id,
                'username' => $this->username,
                'roles' => $this->roles,
                'password' => $this->password,
                'proposals' => $this->proposals,
                'votes' => $this->votes,
                'email' => $this->email,
                'forums' => $this->forums,
                'delegationsFrom' => $this->delegationsFrom,
                'delegationsTo' => $this->delegationsTo,
                'workshops' => $this->workshops,
                'categories' => $this->categories,
                'notifications' => $this->notifications,
                'isAllowedEmails' => $this->isAllowedEmails,
                'image' => $this->image,
                'imageFile' => $this->imageFile = base64_encode($this->imageFile),
                'updatedAt' => $this->updatedAt,
            ];
        }

        public function __unserialize(array $data): User
        {
            $this->id = $data['id'];
            $this->username = $data['username'];
            $this->roles = $data['roles'];
            $this->password = $data['password'];
            $this->proposals = $data['proposals'];
            $this->votes = $data['votes'];
            $this->email = $data['email'];
            $this->forums = $data['forums'];
            $this->delegationsFrom= $data['delegationsFrom'];
            $this->delegationsTo = $data['delegationsTo'];
            $this->workshops = $data['workshops'];
            $this->categories = $data['categories'];
            $this->notifications = $data['notifications'];
            $this->isAllowedEmails = $data['isAllowedEmails'];
            $this->image = $data['image'];
            $this->imageFile = base64_decode($this->imageFile);
            $this->updatedAt = $data['updatedAt'];

            return $this;
        }





    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Request[]
     */
    public function getRequests(): Collection
    {
        return $this->requests;
    }

    public function addRequest(Request $request): self
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            $request->setUser($this);
        }

        return $this;
    }

    public function removeRequest(Request $request): self
    {
        if ($this->requests->removeElement($request)) {
            // set the owning side to null (unless already changed)
            if ($request->getUser() === $this) {
                $request->setUser(null);
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
            $report->setUser($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getUser() === $this) {
                $report->setUser(null);
            }
        }

        return $this;
    }



}
