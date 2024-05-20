<?php

namespace App\Entity;

use App\Repository\WarehouseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WarehouseRepository::class)]
class Warehouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'warehouses')]
    private ?Company $company = null;

    #[ORM\OneToMany(targetEntity: ArticleWarehouse::class, mappedBy: 'warehouse')]
    private Collection $articleWarehouses;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    public function __construct()
    {
        $this->articleWarehouses = new ArrayCollection();
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

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
            $articleWarehouse->setWarehouse($this);
        }

        return $this;
    }

    public function removeArticleWarehouse(ArticleWarehouse $articleWarehouse): static
    {
        if ($this->articleWarehouses->removeElement($articleWarehouse)) {
            // set the owning side to null (unless already changed)
            if ($articleWarehouse->getWarehouse() === $this) {
                $articleWarehouse->setWarehouse(null);
            }
        }

        return $this;
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
}
