<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $unit = null;

    #[ORM\ManyToOne]
    private ?TaxRateType $tax_rate_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $producer = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?ArticleType $article_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_url = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?ArticleCategory $article_category = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foreign_title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $barcode = null;

//    #[ORM\Column(length: 255, nullable: true)]
//    private ?string $producer_title = null;

    #[ORM\Column(nullable: true)]
    private ?int $customs_tariff = null;

    #[ORM\Column(nullable: true)]
    private ?bool $eco_tax = null;

    #[ORM\Column(nullable: true)]
    private ?bool $excise_tax = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $declaration = null;

    #[ORM\OneToMany(targetEntity: ArticleWarehouse::class, mappedBy: 'article')]
    private Collection $articleWarehouses;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Company $company = null;

    #[ORM\Column(nullable: true)]
    private ?bool $active = null;

    #[ORM\Column(nullable: true)]
    private ?int $supplies_amount_max = null;

    #[ORM\Column(nullable: true)]
    private ?int $supplies_amount_min = null;

    public function __construct()
    {
        $this->articleWarehouses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

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

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getTaxRateType(): ?TaxRateType
    {
        return $this->tax_rate_type;
    }

    public function setTaxRateType(?TaxRateType $tax_rate_type): static
    {
        $this->tax_rate_type = $tax_rate_type;

        return $this;
    }

    public function getProducer(): ?string
    {
        return $this->producer;
    }

    public function setProducer(string $producer): static
    {
        $this->producer = $producer;

        return $this;
    }

    public function getArticleType(): ?ArticleType
    {
        return $this->article_type;
    }

    public function setArticleType(?ArticleType $article_type): static
    {
        $this->article_type = $article_type;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): static
    {
        $this->image_url = $image_url;

        return $this;
    }

    public function getArticleCategory(): ?ArticleCategory
    {
        return $this->article_category;
    }

    public function setArticleCategory(?ArticleCategory $article_category): static
    {
        $this->article_category = $article_category;

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

    public function getForeignTitle(): ?string
    {
        return $this->foreign_title;
    }

    public function setForeignTitle(?string $foreign_title): static
    {
        $this->foreign_title = $foreign_title;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): static
    {
        $this->barcode = $barcode;

        return $this;
    }

//    public function getProducerTitle(): ?string
//    {
//        return $this->producer_title;
//    }
//
//    public function setProducerTitle(?string $producer_title): static
//    {
//        $this->producer_title = $producer_title;
//
//        return $this;
//    }

    public function getCustomsTariff(): ?int
    {
        return $this->customs_tariff;
    }

    public function setCustomsTariff(?int $customs_tariff): static
    {
        $this->customs_tariff = $customs_tariff;

        return $this;
    }

    public function isEcoTax(): ?bool
    {
        return $this->eco_tax;
    }

    public function setEcoTax(?bool $eco_tax): static
    {
        $this->eco_tax = $eco_tax;

        return $this;
    }

    public function isExciseTax(): ?bool
    {
        return $this->excise_tax;
    }

    public function setExciseTax(?bool $excise_tax): static
    {
        $this->excise_tax = $excise_tax;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDeclaration(): ?string
    {
        return $this->declaration;
    }

    public function setDeclaration(?string $declaration): static
    {
        $this->declaration = $declaration;

        return $this;
    }

    /**
     * @return Collection<int, ArticleWarehouse>
     */
    public function getArticleWarehouses(): Collection
    {
        return $this->articleWarehouses;
    }

    public function addArticleWarehouse(ArticleWarehouse $articleWarehouse): static
    {
        if (!$this->articleWarehouses->contains($articleWarehouse)) {
            $this->articleWarehouses->add($articleWarehouse);
            $articleWarehouse->setArticle($this);
        }

        return $this;
    }

    public function removeArticleWarehouse(ArticleWarehouse $articleWarehouse): static
    {
        if ($this->articleWarehouses->removeElement($articleWarehouse)) {
            // set the owning side to null (unless already changed)
            if ($articleWarehouse->getArticle() === $this) {
                $articleWarehouse->setArticle(null);
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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getSuppliesAmountMax(): ?int
    {
        return $this->supplies_amount_max;
    }

    public function setSuppliesAmountMax(?int $supplies_amount_max): static
    {
        $this->supplies_amount_max = $supplies_amount_max;

        return $this;
    }

    public function getSuppliesAmountMin(): ?int
    {
        return $this->supplies_amount_min;
    }

    public function setSuppliesAmountMin(?int $supplies_amount_min): static
    {
        $this->supplies_amount_min = $supplies_amount_min;

        return $this;
    }
}
