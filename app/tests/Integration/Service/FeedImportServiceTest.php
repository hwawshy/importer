<?php

namespace App\Tests\Integration\Service;

use App\Entity\Product;
use App\Enumeration\DatabaseEnumeration;
use App\Enumeration\FormatEnumeration;
use App\Repository\MySQLProductRepository;
use App\Service\FeedImportService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FeedImportServiceTest extends KernelTestCase
{
    private MySQLProductRepository $repository;

    private FeedImportService $feedImportService;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var MySQLProductRepository $repository */
        $repository = $container->get(MySQLProductRepository::class);
        $this->repository = $repository;

        /** @var FeedImportService $feedImportService */
        $feedImportService = $container->get(FeedImportService::class);
        $this->feedImportService = $feedImportService;
    }

    public function testCanImportFeed(): void
    {
        $this->feedImportService->import(__DIR__ . '/Resources/feed.csv', FormatEnumeration::CSV, DatabaseEnumeration::MYSQL);
        /** @var Product[] $products */
        $products = $this->repository->findAll();

        $this->assertSame(4, count($products));
        $this->assertSame('07034621736823', $products[0]->getGtin());
        $this->assertSame('es', $products[1]->getLanguage());
        $this->assertSame(57906, $products[2]->getPrice());
        $this->assertSame(97, $products[3]->getStock());
    }

    public function tearDown(): void
    {
        $this->repository->deleteAll();
    }
}
