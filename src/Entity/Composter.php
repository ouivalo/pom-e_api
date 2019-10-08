<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * Composter. Lieux ou l'on transform les bio déchets en composte
 *
 * @ORM\Entity
 * @ApiResource(
 *     normalizationContext={"groups"={"composter"}}
 * )
 */
class Composter
{
    /**
     * @var int The id of the composter
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"composter"})
     */
    private $id;


    /**
     * @var string The name of the composter
     *
     * @ORM\Column
     * @Groups({"composter"})
     */
    public $name;

    /**
     * @var string The short description of the composter to be shown on the composter page
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"composter"})
     */
    public $short_description;

    /**
     * @var string The description of the composter to be shown on the composter page
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"composter"})
     */
    public $description;

    /**
     * @var string The address of the composter to be shown on the composter page
     *
     * @ORM\Column(type="text")
     * @Groups({"composter"})
     */
    public $address;

    /**
     * @var float The latitude of the composter
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"composter"})
     */
    public $lat;

    /**
     * @var float The longitude of the composter
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"composter"})
     */
    public $lng;

    /**
     * @var Permanence[] permancence of the composter
     *
     * @ORM\OneToMany(targetEntity="Permanence", mappedBy="composter")
     */
    public $permanences;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commune", inversedBy="composters")
     */
    private $commune;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pole", inversedBy="composters")
     */
    private $pole;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quartier", inversedBy="composters")
     */
    private $quartier;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PavilionsVolume", inversedBy="composters")
     */
    private $pavilionsVolume;

    /**
     * Name of the Maitre Composter
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="mcComposters")
     */
    private $mc;


    public function __construct()
    {
        $this->permanences = new ArrayCollection();
    }

    public function __toString() {
        return $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
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
            $permanence->setComposter($this);
        }

        return $this;
    }

    public function removePermanence(Permanence $permanence): self
    {
        if ($this->permanences->contains($permanence)) {
            $this->permanences->removeElement($permanence);
            // set the owning side to null (unless already changed)
            if ($permanence->getComposter() === $this) {
                $permanence->setComposter(null);
            }
        }

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): self
    {
        $this->short_description = $short_description;

        return $this;
    }

    public function getCommune(): ?Commune
    {
        return $this->commune;
    }

    public function setCommune(?Commune $commune): self
    {
        $this->commune = $commune;

        return $this;
    }

    public function getPole(): ?Pole
    {
        return $this->pole;
    }

    public function setPole(?Pole $pole): self
    {
        $this->pole = $pole;

        return $this;
    }

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): self
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getPavilionsVolume(): ?PavilionsVolume
    {
        return $this->pavilionsVolume;
    }

    public function setPavilionsVolume(?PavilionsVolume $pavilionsVolume): self
    {
        $this->pavilionsVolume = $pavilionsVolume;

        return $this;
    }

    public function getMc(): ?User
    {
        return $this->mc;
    }

    public function setMc(?User $mc): self
    {
        $this->mc = $mc;

        return $this;
    }
}