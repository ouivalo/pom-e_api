<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     attributes={"security"="is_granted('ROLE_ADMIN')"},
 *     normalizationContext={"groups"={"approvisionnementBroyat"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\ApprovisionnementBroyatRepository")
 */
class ApprovisionnementBroyat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"approvisionnementBroyat", "composter", "livraison"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"approvisionnementBroyat", "composter", "livraison"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Composter", mappedBy="approvisionnementBroyat")
     */
    private $composters;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LivraisonBroyat", mappedBy="livreur")
     */
    private $livraisonBroyats;

    public function __construct()
    {
        $this->composters = new ArrayCollection();
        $this->livraisonBroyats = new ArrayCollection();
    }

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
            $composter->setApprovisionnementBroyat($this);
        }

        return $this;
    }

    public function removeComposter(Composter $composter): self
    {
        if ($this->composters->contains($composter)) {
            $this->composters->removeElement($composter);
            // set the owning side to null (unless already changed)
            if ($composter->getApprovisionnementBroyat() === $this) {
                $composter->setApprovisionnementBroyat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LivraisonBroyat[]
     */
    public function getLivraisonBroyats(): Collection
    {
        return $this->livraisonBroyats;
    }

    public function addLivraisonBroyat(LivraisonBroyat $livraisonBroyat): self
    {
        if (!$this->livraisonBroyats->contains($livraisonBroyat)) {
            $this->livraisonBroyats[] = $livraisonBroyat;
            $livraisonBroyat->setLivreur($this);
        }

        return $this;
    }

    public function removeLivraisonBroyat(LivraisonBroyat $livraisonBroyat): self
    {
        if ($this->livraisonBroyats->contains($livraisonBroyat)) {
            $this->livraisonBroyats->removeElement($livraisonBroyat);
            // set the owning side to null (unless already changed)
            if ($livraisonBroyat->getLivreur() === $this) {
                $livraisonBroyat->setLivreur(null);
            }
        }

        return $this;
    }
}
