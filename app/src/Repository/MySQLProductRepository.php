<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class MySQLProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param Product[] $products
     */
    public function persistProducts(array $products): void
    {
        foreach ($products as $product) {
            $this->getEntityManager()->persist($product);
        }

        $this->getEntityManager()->flush();
    }

    public function getDatabase(): string
    {
        return 'mysql';
    }

    public function deleteAll(): void
    {
        $this->getEntityManager()->getConnection()->executeStatement(
            "DELETE FROM `product`"
        );
    }
}
