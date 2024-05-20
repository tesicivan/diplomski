<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
class Partner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $post_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $identification_number = null;

    #[ORM\ManyToMany(targetEntity: Company::class, inversedBy: 'partners')]
    private Collection $company;

    #[ORM\OneToMany(targetEntity: Bill::class, mappedBy: 'partner')]
    private Collection $bills;

    #[ORM\OneToMany(targetEntity: PayingDaysRebate::class, mappedBy: 'partner')]
    private Collection $payingDaysRebates;

    public function __construct()
    {
        $this->company = new ArrayCollection();
        $this->bills = new ArrayCollection();
        $this->payingDaysRebates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->post_code;
    }

    public function setPostCode(?string $post_code): static
    {
        $this->post_code = $post_code;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getTin(): ?string
    {
        return $this->tin;
    }

    public function setTin(?string $tin): static
    {
        $this->tin = $tin;

        return $this;
    }

    public function getIdentificationNumber(): ?string
    {
        return $this->identification_number;
    }

    public function setIdentificationNumber(?string $identification_number): static
    {
        $this->identification_number = $identification_number;

        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompany(): Collection
    {
        return $this->company;
    }

    public function addCompany(Company $company): static
    {
        if (!$this->company->contains($company)) {
            $this->company->add($company);
        }

        return $this;
    }

    public function removeCompany(Company $company): static
    {
        $this->company->removeElement($company);

        return $this;
    }

    /**
     * @return Collection<int, Bill>
     */
    public function getBills(): Collection
    {
        return $this->bills;
    }

    public function addBill(Bill $bill): static
    {
        if (!$this->bills->contains($bill)) {
            $this->bills->add($bill);
            $bill->setPartner($this);
        }

        return $this;
    }

    public function removeBill(Bill $bill): static
    {
        if ($this->bills->removeElement($bill)) {
            // set the owning side to null (unless already changed)
            if ($bill->getPartner() === $this) {
                $bill->setPartner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PayingDaysRebate>
     */
    public function getPayingDaysRebates(): Collection
    {
        return $this->payingDaysRebates;
    }

    public function addPayingDaysRebate(PayingDaysRebate $payingDaysRebate): static
    {
        if (!$this->payingDaysRebates->contains($payingDaysRebate)) {
            $this->payingDaysRebates->add($payingDaysRebate);
            $payingDaysRebate->setPartner($this);
        }

        return $this;
    }

    public function removePayingDaysRebate(PayingDaysRebate $payingDaysRebate): static
    {
        if ($this->payingDaysRebates->removeElement($payingDaysRebate)) {
            // set the owning side to null (unless already changed)
            if ($payingDaysRebate->getPartner() === $this) {
                $payingDaysRebate->setPartner(null);
            }
        }

        return $this;
    }
}
