<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\CreateConsumerAction;


/**
 * @ApiResource(
 *     collectionOperations={
 *         "post"={
 *             "controller"=CreateConsumerAction::class,
 *             "access_control"="is_granted('ROLE_USER')",
 *         },
 *         "get"
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ConsumerRepository")
 * @ORM\EntityListeners({"App\EventListener\ConsumerListener"})
 * @ApiFilter(SearchFilter::class, properties={"email" : "partial", "composters", "exact"})
 */
class Consumer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Composter", inversedBy="consumers")
     */
    private $composters;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $mailjetId;

    /**
     * @var array
     */
    private $mailjetContactsLists;

    public function __construct()
    {
        $this->composters = new ArrayCollection();
        $this->mailjetContactsLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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
     * @return Collection|Composter[]
     */
    public function getComposters(): Collection
    {
        return $this->composters;
    }

    public function addComposter(Composter $composter): self
    {
        if (!$this->composters->contains($composter)) {
            $this->composters[] = $composter;
        }

        return $this;
    }

    public function removeComposter(Composter $composter): self
    {
        if ($this->composters->contains($composter)) {
            $this->composters->removeElement($composter);
        }

        return $this;
    }

    public function getMailjetId(): ?int
    {
        return $this->mailjetId;
    }

    public function setMailjetId(?int $mailjetId): self
    {
        $this->mailjetId = $mailjetId;

        return $this;
    }

    /**
     * @return array
     */
    public function getMailjetContactsLists(): array
    {
        return $this->mailjetContactsLists;
    }

    /**
     * @param array $mailjetContactsLists
     */
    public function setMailjetContactsLists(array $mailjetContactsLists): void
    {
        $this->mailjetContactsLists = $mailjetContactsLists;
    }
}
