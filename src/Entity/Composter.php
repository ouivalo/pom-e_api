<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Composter. Lieux ou l'on transform les bio déchets en composte
 *
 * @ORM\Entity(repositoryClass="App\Repository\ComposterRepository")
 * @ApiResource(
 *     normalizationContext={"groups"={"composter"}}
 * )
 * @ApiFilter(SearchFilter::class, properties={
 *     "commune"    : "exact",
 *     "quartier"   : "exact",
 *     "pole"       : "exact",
 *     "categorie"  : "exact",
 *     "name"       : "partial"
 * })
 * @ApiFilter(OrderFilter::class, properties={"id", "DateMiseEnRoute"}, arguments={"orderParameterName"="order"})
 */
class Composter
{
    /**
     * @var int The id of the composter
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=false)
     */
    private $id;

    /**
     * @var int The id of the composter for the backoffice
     * @Groups({"composter", "suivis", "livraison"})
     */
    private $rid;

    /**
     * @var string The name of the composter
     *
     * @ORM\Column
     * @Groups({"composter", "suivis", "livraison", "reparation", "permanence","userComposter", "contact"})
     */
    private $name;

    /**
     * @var string The slug of the composter
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     * @Groups({"composter", "suivis", "livraison", "reparation", "permanence"})
     * @ApiProperty(identifier=true)
     */
    private $slug;


    /**
     * @var string The short description of the composter to be shown on the composter page
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"composter"})
     */
    private $shortDescription;

    /**
     * @var string The description of the composter to be shown on the composter page
     *
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"composter"})
     */
    private $description;

    /**
     * @var string The address of the composter to be shown on the composter page
     *
     * @ORM\Column(type="text")
     * @Groups({"composter"})
     */
    private $address;

    /**
     * @var float The latitude of the composter
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"composter"})
     */
    private $lat;

    /**
     * @var float The longitude of the composter
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"composter"})
     */
    private $lng;

    /**
     * @var Permanence[] permancence of the composter
     *
     * @ORM\OneToMany(targetEntity="Permanence", mappedBy="composter")
     */
    private $permanences;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commune", inversedBy="composters")
     * @Groups({"composter"})
     */
    private $commune;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pole", inversedBy="composters")
     * @Groups({"composter"})
     */
    private $pole;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Quartier", inversedBy="composters")
     * @Groups({"composter"})
     */
    private $quartier;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Equipement", inversedBy="composters")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"composter"})
     */
    private $equipement;

    /**
     * Name of the Maitre Composter
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="mcComposters")
     * @Groups({"composter"})
     */
    private $mc;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"composter"})
     */
    private $openingProcedures;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ApprovisionnementBroyat", inversedBy="composters")
     */
    private $approvisionnementBroyat;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"composter"})
     */
    private $DateMiseEnRoute;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"composter"})
     */
    private $DateInauguration;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"composter"})
     */
    private $DateInstallation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LivraisonBroyat", mappedBy="composter", orphanRemoval=true)
     * @Groups({"composter"})
     */
    private $livraisonBroyats;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Suivi", mappedBy="composter", orphanRemoval=true)
     * @Groups({"composter"})
     */
    private $suivis;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reparation", mappedBy="composter")
     * @Groups({"composter"})
     */
    private $reparations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="composteurs")
     * @Groups({"composter"})
     */
    private $categorie;

    /**
     * @ORM\Column(type="enumstatus", options={"default":"Active"})
     * @Groups({"composter"})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserComposter", mappedBy="composter", orphanRemoval=true)
     */
    private $userComposters;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ComposterContact", mappedBy="composter", orphanRemoval=true)
     */
    private $composterContacts;

    /**
     * @ORM\Column(type="boolean", options={"default":1})
     * @Groups({"composter"})
     */
    private $acceptNewMembers;



    /**
     * @var MediaObject|null
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\MediaObject")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"composter"})
     * @ApiProperty(iri="http://schema.org/image")
     */
    private $image;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"composter"})
     */
    private $permanencesRule;

    /**
     * @ORM\Column(type="enumbroyat", options={"default":"Full"})
     * @Groups({"composter"})
     */
    private $broyatLevel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Financeur", inversedBy="composters")
     * @Groups({"composter"})
     */
    private $financeur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"composter"})
     */
    private $serialNumber;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Contact", mappedBy="composters")
     */
    private $contacts;


    public function __construct()
    {
        $this->permanences = new ArrayCollection();
        $this->livraisonBroyats = new ArrayCollection();
        $this->suivis = new ArrayCollection();
        $this->reparations = new ArrayCollection();
        $this->status = 'Active';
        $this->userComposters = new ArrayCollection();
        $this->composterContacts = new ArrayCollection();
        $this->acceptNewMembers = true;
        $this->broyatLevel = 'Full';
        $this->contacts = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRid(): ?int
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
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

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): self
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
        return $this->shortDescription;
    }

    public function setShortDescription(?string $short_description): self
    {
        $this->shortDescription = $short_description;

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

    public function getEquipement(): ?Equipement
    {
        return $this->equipement;
    }

    public function setEquipement(?Equipement $equipement): self
    {
        $this->equipement = $equipement;

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

    public function getOpeningProcedures(): ?string
    {
        return $this->openingProcedures;
    }

    public function setOpeningProcedures(?string $openingProcedures): self
    {
        $this->openingProcedures = $openingProcedures;

        return $this;
    }

    public function getApprovisionnementBroyat(): ?ApprovisionnementBroyat
    {
        return $this->approvisionnementBroyat;
    }

    public function setApprovisionnementBroyat(?ApprovisionnementBroyat $approvisionnementBroyat): self
    {
        $this->approvisionnementBroyat = $approvisionnementBroyat;

        return $this;
    }

    public function getDateMiseEnRoute(): ?\DateTimeInterface
    {
        return $this->DateMiseEnRoute;
    }

    public function setDateMiseEnRoute(?\DateTimeInterface $DateMiseEnRoute): self
    {
        $this->DateMiseEnRoute = $DateMiseEnRoute;

        return $this;
    }

    public function getDateInauguration(): ?\DateTimeInterface
    {
        return $this->DateInauguration;
    }

    public function setDateInauguration(?\DateTimeInterface $DateInauguration): self
    {
        $this->DateInauguration = $DateInauguration;

        return $this;
    }

    public function getDateInstallation(): ?\DateTimeInterface
    {
        return $this->DateInstallation;
    }

    public function setDateInstallation(?\DateTimeInterface $DateInstallation): self
    {
        $this->DateInstallation = $DateInstallation;

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
            $livraisonBroyat->setComposter($this);
        }

        return $this;
    }

    public function removeLivraisonBroyat(LivraisonBroyat $livraisonBroyat): self
    {
        if ($this->livraisonBroyats->contains($livraisonBroyat)) {
            $this->livraisonBroyats->removeElement($livraisonBroyat);
            // set the owning side to null (unless already changed)
            if ($livraisonBroyat->getComposter() === $this) {
                $livraisonBroyat->setComposter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Suivi[]
     */
    public function getSuivis(): Collection
    {
        return $this->suivis;
    }

    public function addSuivi(Suivi $suivi): self
    {
        if (!$this->suivis->contains($suivi)) {
            $this->suivis[] = $suivi;
            $suivi->setComposter($this);
        }

        return $this;
    }

    public function removeSuivi(Suivi $suivi): self
    {
        if ($this->suivis->contains($suivi)) {
            $this->suivis->removeElement($suivi);
            // set the owning side to null (unless already changed)
            if ($suivi->getComposter() === $this) {
                $suivi->setComposter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reparation[]
     */
    public function getReparations(): Collection
    {
        return $this->reparations;
    }

    public function addReparation(Reparation $reparation): self
    {
        if (!$this->reparations->contains($reparation)) {
            $this->reparations[] = $reparation;
            $reparation->setComposter($this);
        }

        return $this;
    }

    public function removeReparation(Reparation $reparation): self
    {
        if ($this->reparations->contains($reparation)) {
            $this->reparations->removeElement($reparation);
            // set the owning side to null (unless already changed)
            if ($reparation->getComposter() === $this) {
                $reparation->setComposter(null);
            }
        }

        return $this;
    }


    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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
            $userComposter->setComposter($this);
        }

        return $this;
    }

    public function removeUserComposter(UserComposter $userComposter): self
    {
        if ($this->userComposters->contains($userComposter)) {
            $this->userComposters->removeElement($userComposter);
            // set the owning side to null (unless already changed)
            if ($userComposter->getComposter() === $this) {
                $userComposter->setComposter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ComposterContact[]
     */
    public function getComposterContacts(): Collection
    {
        return $this->composterContacts;
    }

    public function addComposterContact(ComposterContact $composterContact): self
    {
        if (!$this->composterContacts->contains($composterContact)) {
            $this->composterContacts[] = $composterContact;
            $composterContact->setComposter($this);
        }

        return $this;
    }

    public function removeComposterContact(ComposterContact $composterContact): self
    {
        if ($this->composterContacts->contains($composterContact)) {
            $this->composterContacts->removeElement($composterContact);
            // set the owning side to null (unless already changed)
            if ($composterContact->getComposter() === $this) {
                $composterContact->setComposter(null);
            }
        }

        return $this;
    }

    public function getAcceptNewMembers(): ?bool
    {
        return $this->acceptNewMembers;
    }

    public function setAcceptNewMembers(bool $acceptNewMembers): self
    {
        $this->acceptNewMembers = $acceptNewMembers;

        return $this;
    }

    public function getImage(): ?MediaObject
    {
        return $this->image;
    }

    public function setImage(?MediaObject $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getPermanencesRule(): ?string
    {
        return $this->permanencesRule;
    }

    public function setPermanencesRule(?string $permanencesRule): self
    {
        $this->permanencesRule = $permanencesRule;

        return $this;
    }

    public function getBroyatLevel()
    {
        return $this->broyatLevel;
    }

    public function setBroyatLevel($broyatLevel): self
    {
        $this->broyatLevel = $broyatLevel;

        return $this;
    }

    public function getFinanceur(): ?Financeur
    {
        return $this->financeur;
    }

    public function setFinanceur(?Financeur $financeur): self
    {
        $this->financeur = $financeur;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(?string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->addComposter($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->contains($contact)) {
            $this->contacts->removeElement($contact);
            $contact->removeComposter($this);
        }

        return $this;
    }
}
