<?php

namespace App\Entity;

use App\Repository\BillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillRepository::class)]
class Bill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $payed = null;

    #[ORM\ManyToOne(inversedBy: 'bills')]
    private ?PayingType $paying_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $last_name = null;

    #[ORM\ManyToOne(inversedBy: 'bills')]
    private ?Customer $customer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $customer_id_number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slip_bill_number = null;

    #[ORM\ManyToOne(inversedBy: 'bills')]
    private ?Partner $partner = null;

    #[ORM\OneToMany(targetEntity: ArticleAmount::class, mappedBy: 'bill')]
    private Collection $articleAmounts;

    #[ORM\ManyToOne(inversedBy: 'bills')]
    private ?Company $company = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    public function __construct()
    {
        $this->articleAmounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPayed(): ?int
    {
        return $this->payed;
    }

    public function setPayed(?int $payed): static
    {
        $this->payed = $payed;

        return $this;
    }

    public function getPayingType(): ?PayingType
    {
        return $this->paying_type;
    }

    public function setPayingType(?PayingType $paying_type): static
    {
        $this->paying_type = $paying_type;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCustomerIdNumber(): ?string
    {
        return $this->customer_id_number;
    }

    public function setCustomerIdNumber(?string $customer_id_number): static
    {
        $this->customer_id_number = $customer_id_number;

        return $this;
    }

    public function getSlipBillNumber(): ?string
    {
        return $this->slip_bill_number;
    }

    public function setSlipBillNumber(?string $slip_bill_number): static
    {
        $this->slip_bill_number = $slip_bill_number;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return Collection<int, ArticleAmount>
     */
    public function getArticleAmounts(): Collection
    {
        return $this->articleAmounts;
    }

    public function addArticleAmount(ArticleAmount $articleAmount): static
    {
        if (!$this->articleAmounts->contains($articleAmount)) {
            $this->articleAmounts->add($articleAmount);
            $articleAmount->setBill($this);
        }

        return $this;
    }

    public function removeArticleAmount(ArticleAmount $articleAmount): static
    {
        if ($this->articleAmounts->removeElement($articleAmount)) {
            // set the owning side to null (unless already changed)
            if ($articleAmount->getBill() === $this) {
                $articleAmount->setBill(null);
            }
        }

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

}
