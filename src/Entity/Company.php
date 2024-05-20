<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $last_name = null;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo_url = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_active = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_configured = null;

    #[ORM\Column(nullable: true)]
    private ?bool $vat = null;

    #[ORM\OneToMany(targetEntity: BankAccount::class, mappedBy: 'company')]
    private Collection $bankAccounts;

    #[ORM\OneToMany(targetEntity: Warehouse::class, mappedBy: 'company')]
    private Collection $warehouses;

    #[ORM\OneToMany(targetEntity: CashRegister::class, mappedBy: 'company')]
    private Collection $cashRegisters;

    #[ORM\ManyToMany(targetEntity: Partner::class, mappedBy: 'company')]
    private Collection $partners;

    #[ORM\Column(nullable: true)]
    private ?bool $editedByAdmin = null;

    #[ORM\OneToMany(targetEntity: Bill::class, mappedBy: 'company')]
    private Collection $bills;

    #[ORM\OneToMany(targetEntity: Article::class, mappedBy: 'company')]
    private Collection $articles;

    #[ORM\ManyToOne(inversedBy: 'companies')]
    private ?CompanyCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'companies')]
    private ?ActiviyCode $activity_code = null;

    #[ORM\OneToMany(targetEntity: PayingDaysRebate::class, mappedBy: 'company')]
    private Collection $payingDaysRebates;

    #[ORM\OneToMany(targetEntity: TableSchedule::class, mappedBy: 'company')]
    private Collection $tableSchedules;

    #[ORM\OneToMany(targetEntity: ArticleCategory::class, mappedBy: 'company')]
    private Collection $articleCategories;

    public function __construct()
    {
        $this->bankAccounts = new ArrayCollection();
        $this->warehouses = new ArrayCollection();
        $this->cashRegisters = new ArrayCollection();
        $this->partners = new ArrayCollection();
        $this->bills = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->payingDaysRebates = new ArrayCollection();
        $this->tableSchedules = new ArrayCollection();
        $this->articleCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
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

    public function getLogoUrl(): ?string
    {
        return $this->logo_url;
    }

    public function setLogoUrl(?string $logo_url): static
    {
        $this->logo_url = $logo_url;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(?bool $is_active): static
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function isIsConfigured(): ?bool
    {
        return $this->is_configured;
    }

    public function setIsConfigured(?bool $is_configured): static
    {
        $this->is_configured = $is_configured;

        return $this;
    }

    public function isVat(): ?bool
    {
        return $this->vat;
    }

    public function setVat(?bool $vat): static
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * @return Collection<int, BankAccount>
     */
    public function getBankAccounts(): Collection
    {
        return $this->bankAccounts;
    }

    public function addBankAccount(BankAccount $bankAccount): static
    {
        if (!$this->bankAccounts->contains($bankAccount)) {
            $this->bankAccounts->add($bankAccount);
            $bankAccount->setCompany($this);
        }

        return $this;
    }

    public function removeBankAccount(BankAccount $bankAccount): static
    {
        if ($this->bankAccounts->removeElement($bankAccount)) {
            // set the owning side to null (unless already changed)
            if ($bankAccount->getCompany() === $this) {
                $bankAccount->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Warehouse>
     */
    public function getWarehouses(): Collection
    {
        return $this->warehouses;
    }

    public function addWarehouse(Warehouse $warehouse): static
    {
        if (!$this->warehouses->contains($warehouse)) {
            $this->warehouses->add($warehouse);
            $warehouse->setCompany($this);
        }

        return $this;
    }

    public function removeWarehouse(Warehouse $warehouse): static
    {
        if ($this->warehouses->removeElement($warehouse)) {
            // set the owning side to null (unless already changed)
            if ($warehouse->getCompany() === $this) {
                $warehouse->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CashRegister>
     */
    public function getCashRegisters(): Collection
    {
        return $this->cashRegisters;
    }

    public function addCashRegister(CashRegister $cashRegister): static
    {
        if (!$this->cashRegisters->contains($cashRegister)) {
            $this->cashRegisters->add($cashRegister);
            $cashRegister->setCompany($this);
        }

        return $this;
    }

    public function removeCashRegister(CashRegister $cashRegister): static
    {
        if ($this->cashRegisters->removeElement($cashRegister)) {
            // set the owning side to null (unless already changed)
            if ($cashRegister->getCompany() === $this) {
                $cashRegister->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Partner>
     */
    public function getPartners(): Collection
    {
        return $this->partners;
    }

    public function addPartner(Partner $partner): static
    {
        if (!$this->partners->contains($partner)) {
            $this->partners->add($partner);
            $partner->addCompany($this);
        }

        return $this;
    }

    public function removePartner(Partner $partner): static
    {
        if ($this->partners->removeElement($partner)) {
            $partner->removeCompany($this);
        }

        return $this;
    }

    public function isEditedByAdmin(): ?bool
    {
        return $this->editedByAdmin;
    }

    public function setEditedByAdmin(?bool $editedByAdmin): static
    {
        $this->editedByAdmin = $editedByAdmin;

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
            $bill->setCompany($this);
        }

        return $this;
    }

    public function removeBill(Bill $bill): static
    {
        if ($this->bills->removeElement($bill)) {
            // set the owning side to null (unless already changed)
            if ($bill->getCompany() === $this) {
                $bill->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setCompany($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getCompany() === $this) {
                $article->setCompany(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?CompanyCategory
    {
        return $this->category;
    }

    public function setCategory(?CompanyCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getActivityCode(): ?ActiviyCode
    {
        return $this->activity_code;
    }

    public function setActivityCode(?ActiviyCode $activity_code): static
    {
        $this->activity_code = $activity_code;

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
            $payingDaysRebate->setCompany($this);
        }

        return $this;
    }

    public function removePayingDaysRebate(PayingDaysRebate $payingDaysRebate): static
    {
        if ($this->payingDaysRebates->removeElement($payingDaysRebate)) {
            // set the owning side to null (unless already changed)
            if ($payingDaysRebate->getCompany() === $this) {
                $payingDaysRebate->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TableSchedule>
     */
    public function getTableSchedules(): Collection
    {
        return $this->tableSchedules;
    }

    public function addTableSchedule(TableSchedule $tableSchedule): static
    {
        if (!$this->tableSchedules->contains($tableSchedule)) {
            $this->tableSchedules->add($tableSchedule);
            $tableSchedule->setCompany($this);
        }

        return $this;
    }

    public function removeTableSchedule(TableSchedule $tableSchedule): static
    {
        if ($this->tableSchedules->removeElement($tableSchedule)) {
            // set the owning side to null (unless already changed)
            if ($tableSchedule->getCompany() === $this) {
                $tableSchedule->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ArticleCategory>
     */
    public function getArticleCategories(): Collection
    {
        return $this->articleCategories;
    }

    public function addArticleCategory(ArticleCategory $articleCategory): static
    {
        if (!$this->articleCategories->contains($articleCategory)) {
            $this->articleCategories->add($articleCategory);
            $articleCategory->setCompany($this);
        }

        return $this;
    }

    public function removeArticleCategory(ArticleCategory $articleCategory): static
    {
        if ($this->articleCategories->removeElement($articleCategory)) {
            // set the owning side to null (unless already changed)
            if ($articleCategory->getCompany() === $this) {
                $articleCategory->setCompany(null);
            }
        }

        return $this;
    }

}
