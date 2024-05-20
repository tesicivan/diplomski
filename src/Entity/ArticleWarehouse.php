<?php

namespace App\Entity;

use App\Repository\ArticleWarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleWarehouseRepository::class)]
class ArticleWarehouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'articleWarehouses')]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'articleWarehouses')]
    private ?Warehouse $warehouse = null;

    #[ORM\Column(nullable: true)]
    private ?int $buying_price = null;

    #[ORM\Column(nullable: true)]
    private ?int $selling_price = null;

    #[ORM\Column(nullable: true)]
    private ?int $current_amount = null;

    #[ORM\Column(nullable: true)]
    private ?int $max_amount = null;

    #[ORM\Column(nullable: true)]
    private ?int $min_amount = null;

    #[ORM\OneToMany(targetEntity: ArticleAmount::class, mappedBy: 'article_warehouse')]
    private Collection $articleAmounts;

    public function __construct()
    {
        $this->articleAmounts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): static
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    public function getBuyingPrice(): ?int
    {
        return $this->buying_price;
    }

    public function setBuyingPrice(?int $buying_price): static
    {
        $this->buying_price = $buying_price;

        return $this;
    }

    public function getSellingPrice(): ?int
    {
        return $this->selling_price;
    }

    public function setSellingPrice(?int $selling_price): static
    {
        $this->selling_price = $selling_price;

        return $this;
    }

    public function getCurrentAmount(): ?int
    {
        return $this->current_amount;
    }

    public function setCurrentAmount(?int $current_amount): static
    {
        $this->current_amount = $current_amount;

        return $this;
    }

    public function getMaxAmount(): ?int
    {
        return $this->max_amount;
    }

    public function setMaxAmount(?int $max_amount): static
    {
        $this->max_amount = $max_amount;

        return $this;
    }

    public function getMinAmount(): ?int
    {
        return $this->min_amount;
    }

    public function setMinAmount(?int $min_amount): static
    {
        $this->min_amount = $min_amount;

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
            $articleAmount->setArticleWarehouse($this);
        }

        return $this;
    }

    public function removeArticleAmount(ArticleAmount $articleAmount): static
    {
        if ($this->articleAmounts->removeElement($articleAmount)) {
            // set the owning side to null (unless already changed)
            if ($articleAmount->getArticleWarehouse() === $this) {
                $articleAmount->setArticleWarehouse(null);
            }
        }

        return $this;
    }
}
