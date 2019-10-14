<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"pavilion"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\PavilionsVolumeRepository")
 */
class PavilionsVolume
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"composter", "pavilion"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"composter", "pavilion"})
     */
    private $volume;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Composter", mappedBy="pavilionsVolume")
     */
    private $composters;

    public function __construct()
    {
        $this->composters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVolume(): ?string
    {
        return $this->volume;
    }

    public function setVolume(string $volume): self
    {
        $this->volume = $volume;

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
            $composter->setPavilionsVolume($this);
        }

        return $this;
    }

    public function removeComposter(Composter $composter): self
    {
        if ($this->composters->contains($composter)) {
            $this->composters->removeElement($composter);
            // set the owning side to null (unless already changed)
            if ($composter->getPavilionsVolume() === $this) {
                $composter->setPavilionsVolume(null);
            }
        }

        return $this;
    }
}
