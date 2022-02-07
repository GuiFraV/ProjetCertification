<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $reference;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="commandes")
     */
    private $acheteur;

    /**
     * @ORM\OneToMany(targetEntity=DetailCommande::class, mappedBy="commande")
     */
    private $detailCommandes;

    /**
     * @ORM\OneToOne(targetEntity=PaymentRequest::class, mappedBy="fromOrder", cascade={"persist", "remove"})
     */
    private $paymentRequest;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $fournisseur;



    public function __construct()
    {
        $this->detailCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCreation(): ?\DateTimeInterface
    {
        return $this->creation;
    }

    public function setCreation(\DateTimeInterface $creation): self
    {
        $this->creation = $creation;

        return $this;
    }

    public function getAcheteur(): ?User
    {
        return $this->acheteur;
    }

    public function setAcheteur(?User $acheteur): self
    {
        $this->acheteur = $acheteur;

        return $this;
    }

    /**
     * @return Collection|DetailCommande[]
     */
    public function getDetailCommandes(): Collection
    {
        return $this->detailCommandes;
    }

    public function addDetailCommande(DetailCommande $detailCommande): self
    {
        if (!$this->detailCommandes->contains($detailCommande)) {
            $this->detailCommandes[] = $detailCommande;
            $detailCommande->setCommande($this);
        }

        return $this;
    }

    public function removeDetailCommande(DetailCommande $detailCommande): self
    {
        if ($this->detailCommandes->removeElement($detailCommande)) {
            // set the owning side to null (unless already changed)
            if ($detailCommande->getCommande() === $this) {
                $detailCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getPaymentRequest(): ?PaymentRequest
    {
        return $this->paymentRequest;
    }

    public function setPaymentRequest(?PaymentRequest $paymentRequest): self
    {
        // unset the owning side of the relation if necessary
        if ($paymentRequest === null && $this->paymentRequest !== null) {
            $this->paymentRequest->setFromOrder(null);
        }

        // set the owning side of the relation if necessary
        if ($paymentRequest !== null && $paymentRequest->getFromOrder() !== $this) {
            $paymentRequest->setFromOrder($this);
        }

        $this->paymentRequest = $paymentRequest;

        return $this;
    }

    public function getFournisseur(): ?int
    {
        return $this->fournisseur;
    }

    public function setFournisseur(int $fournisseur): self
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }
}
