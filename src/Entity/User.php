<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_USER')"},
 *     normalizationContext={"groups"={"user", "user:read"}},
 *     denormalizationContext={"groups"={"user", "user:write"}}
 *
 * )
 * @ApiFilter(SearchFilter::class, properties={"email" : "partial"})
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"permanence", "composter"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     * @Groups({"user","permanence", "composter", "userComposter"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user","permanence", "composter", "userComposter"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:write"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups({"user:write"})
     */
    private $password;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Permanence", inversedBy="users")
     */
    private $permanences;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Composter", mappedBy="mc")
     */
    private $mcComposters;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserComposter", mappedBy="user", orphanRemoval=true)
     * @Groups({"user:read"})
     */
    private $userComposters;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"user", "userComposter"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Groups({"user", "userComposter"})
     */
    private $firstname;


    public function __construct()
    {
        $this->permanences = new ArrayCollection();
        $this->mcComposters = new ArrayCollection();
        $this->userComposters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
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
        return $this->password;
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
     * @return Collection|Permanence[]
     */
    public function getPermanences(): Collection
    {
        return $this->permanences;
    }

    public function addPermanence(Permanence $permanence): self
    {
        if (!$this->permanences->contains($permanence)) {
            $this->permanences[] = $permanence;
        }

        return $this;
    }

    public function removePermanence(Permanence $permanence): self
    {
        if ($this->permanences->contains($permanence)) {
            $this->permanences->removeElement($permanence);
        }

        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|Composter[]
     */
    public function getMcComposters(): Collection
    {
        return $this->mcComposters;
    }

    public function addMcComposter(Composter $mcComposter): self
    {
        if (!$this->mcComposters->contains($mcComposter)) {
            $this->mcComposters[] = $mcComposter;
            $mcComposter->setMc($this);
        }

        return $this;
    }

    public function removeMcComposter(Composter $mcComposter): self
    {
        if ($this->mcComposters->contains($mcComposter)) {
            $this->mcComposters->removeElement($mcComposter);
            // set the owning side to null (unless already changed)
            if ($mcComposter->getMc() === $this) {
                $mcComposter->setMc(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserComposter[]
     */
    public function getUserComposters(): Collection
    {
        return $this->userComposters;
    }

    public function addUserComposter(UserComposter $userComposter): self
    {
        if (!$this->userComposters->contains($userComposter)) {
            $this->userComposters[] = $userComposter;
            $userComposter->setUser($this);
        }

        return $this;
    }

    public function removeUserComposter(UserComposter $userComposter): self
    {
        if ($this->userComposters->contains($userComposter)) {
            $this->userComposters->removeElement($userComposter);
            // set the owning side to null (unless already changed)
            if ($userComposter->getUser() === $this) {
                $userComposter->setUser(null);
            }
        }

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }
}
