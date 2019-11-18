<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserComposterRepository")
 */
class UserComposter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userComposters")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"composter"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Composter", inversedBy="userComposters")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user:read"})
     */
    private $composter;

    /**
     * @ORM\Column(type="enumcapability")
     * @Groups({"composter", "user:read"})
     */
    private $capability;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $notif;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $newsletter;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $composterContactReceiver;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getComposter(): ?Composter
    {
        return $this->composter;
    }

    public function setComposter(?Composter $composter): self
    {
        $this->composter = $composter;

        return $this;
    }

    public function getCapability()
    {
        return $this->capability;
    }

    public function setCapability($capability): self
    {
        $this->capability = $capability;

        return $this;
    }

    public function getNotif(): ?bool
    {
        return $this->notif;
    }

    public function setNotif(bool $notif): self
    {
        $this->notif = $notif;

        return $this;
    }

    public function getNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    public function getComposterContactReceiver(): ?bool
    {
        return $this->composterContactReceiver;
    }

    public function setComposterContactReceiver(bool $composterContactReceiver): self
    {
        $this->composterContactReceiver = $composterContactReceiver;

        return $this;
    }
}
