<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'product', options: ['collate' => 'utf8mb4_unicode_ci', 'charset' => 'utf8mb4'])]
#[ORM\Index(name: 'ix_gtin', columns: ['gtin'])]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(length: 14, nullable: false)]
    #[Assert\NotBlank, Assert\Length(exactly: 14)]
    private string $gtin;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank, Assert\Length(max: 255)]
    private string $title;

    #[ORM\Column(type: Types::TEXT, length: 65535, nullable: false)]
    #[Assert\NotBlank, Assert\Length(max: 65535)]
    private string $description;

    #[ORM\Column(type: Types::TEXT, length: 65535, nullable: false)]
    #[Assert\NotBlank, Assert\Length(max: 65535)]
    private string $image;

    #[ORM\Column]
    #[Assert\Positive]
    private int $price;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private int $stock;

    #[ORM\Column(length: 3)]
    #[Assert\NotBlank, Assert\Length(max: 3)]
    private string $language;

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getGtin(): string
    {
        return $this->gtin;
    }

    public function setGtin(string $gtin): static
    {
        $this->gtin = $gtin;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }
}
