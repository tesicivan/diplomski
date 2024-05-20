<?php

namespace App\Entity;

use App\Repository\PayingDaysRebateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PayingDaysRebateRepository::class)]
class PayingDaysRebate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $paying_days = null;

    #[ORM\Column(nullable: true)]
    private ?int $rebate = null;

    #[ORM\ManyToOne(inversedBy: 'payingDaysRebates')]
    private ?Partner $partner = null;

    #[ORM\ManyToOne(inversedBy: 'payingDaysRebates')]
    private ?Company $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPayingDays(): ?int
    {
        return $this->paying_days;
    }

    public function setPayingDays(?int $paying_days): static
    {
        $this->paying_days = $paying_days;

        return $this;
    }

    public function getRebate(): ?int
    {
        return $this->rebate;
    }

    public function setRebate(?int $rebate): static
    {
        $this->rebate = $rebate;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }
}
