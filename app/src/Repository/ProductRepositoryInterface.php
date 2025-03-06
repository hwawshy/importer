<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.product_repository')]
interface ProductRepositoryInterface
{
    /**
     * @param Product[] $products
     */
    public function persistProducts(array $products): void;

    public function getDatabase(): string;
}
