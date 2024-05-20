<?php

namespace App\Entity;

use App\Repository\ArticleAmountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleAmountRepository::class)]
class ArticleAmount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'articleAmounts')]
    private ?ArticleWarehouse $article_warehouse = null;

    #[ORM\Column(nullable: true)]
    private ?int $amount = null;

    #[ORM\Column(nullable: true)]
    private ?int $value = null;

    #[ORM\Column(nullable: true)]
    private ?int $tax = null;

    #[ORM\ManyToOne(inversedBy: 'articleAmounts')]
    private ?Bill $bill = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticleWarehouse(): ?ArticleWarehouse
    {
        return $this->article_warehouse;
    }

    public function setArticleWarehouse(?ArticleWarehouse $article_warehouse): static
    {
        $this->article_warehouse = $article_warehouse;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getTax(): ?int
    {
        return $this->tax;
    }

    public function setTax(?int $tax): static
    {
        $this->tax = $tax;

        return $this;
    }

    public function getBill(): ?Bill
    {
        return $this->bill;
    }

    public function setBill(?Bill $bill): static
    {
        $this->bill = $bill;

        return $this;
    }
}
