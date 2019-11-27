<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\ComposterContactRepository")
 */
class ComposterContact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sentByMailjet;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Composter", inversedBy="composterContacts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $composter;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getSentByMailjet(): ?bool
    {
        return $this->sentByMailjet;
    }

    public function setSentByMailjet(?bool $sentByMailjet): self
    {
        $this->sentByMailjet = $sentByMailjet;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getComposter(): Composter
    {
        return $this->composter;
    }

    public function setComposter(Composter $composter): self
    {
        $this->composter = $composter;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setInitialDate()
    {
        $this->creationDate = new \DateTime();
    }
}
