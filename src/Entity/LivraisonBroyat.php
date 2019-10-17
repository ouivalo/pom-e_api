<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"livraison"}}
 * )
 * @ORM\Entity(repositoryClass="App\Repository\LivraisonBroyatRepository")
 */
class LivraisonBroyat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"livraison"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"livraison"})
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"livraison"})
     */
    private $quantite;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"livraison"})
     */
    private $unite;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"livraison"})
     */
    private $livreur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Composter", inversedBy="livraisonBroyats")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"livraison"})
     */
    private $composter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(string $unite): self
    {
        $this->unite = $unite;

        return $this;
    }

    public function getLivreur(): ?string
    {
        return $this->livreur;
    }

    public function setLivreur(string $livreur): self
    {
        $this->livreur = $livreur;

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
}
