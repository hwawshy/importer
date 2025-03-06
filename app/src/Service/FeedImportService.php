<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Enumeration\DatabaseEnumeration;
use App\Enumeration\FormatEnumeration;
use App\Exception\ConstraintViolationException;
use App\Exception\FeedNormalizerException;
use App\Repository\ProductRepositoryInterface;
use App\Service\FileReader\FileReaderInterface;
use App\Service\Normalizer\FeedNormalizer;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class FeedImportService
{
    private const int CHUNK_SIZE = 20;

    public function __construct(
        #[AutowireIterator('app.file_reader')]
        /** @var iterable<FileReaderInterface> $readers */
        private iterable $readers,
        #[AutowireIterator('app.product_repository')]
        /** @var iterable<ProductRepositoryInterface> $readers */
        private iterable $repositories,
        private FeedNormalizer $feedNormalizer,
        private ValidatorInterface $validator,
        private LoggerInterface $logger
    ) {
    }

    public function import(string $filepath, FormatEnumeration $format, DatabaseEnumeration $database): void
    {
        $realpath = realpath($filepath);
        if ($realpath === false) {
            throw new \InvalidArgumentException(sprintf("Can't find %s", $filepath));
        }

        $this->logger->info(sprintf('Importing feed from %s', $realpath));

        $reader = $this->getReader($format);
        $repository = $this->getRepository($database);

        $i = 0;
        $products = [];

        foreach ($reader->read($realpath) as $row) {
            $i++;
            try {
                $normalizedRow = $this->feedNormalizer->normalizeRow($row);
                $products[] = $this->mapToProduct($normalizedRow);
            } catch (FeedNormalizerException $e) {
                $this->logger->warning(sprintf("Could not normalize row %d: %s", $i, $e->getMessage()));
            } catch (ConstraintViolationException $e) {
                $this->logger->warning(sprintf('Could not map row %d to a product: %s', $i, $e->getMessage()));
            }

            if (count($products) % self::CHUNK_SIZE === 0) {
                $repository->persistProducts($products);
                $products = [];
            }
        }

        if (count($products) > 0) {
            $repository->persistProducts($products);
        }
    }

    /**
     * @throws ConstraintViolationException
     */
    private function mapToProduct(array $row): Product
    {
        $product = new Product();
        $product->setGtin($row['gtin'])
            ->setLanguage($row['language'])
            ->setTitle($row['title'])
            ->setDescription($row['description'])
            ->setImage($row['picture'])
            ->setPrice($row['price'])
            ->setStock($row['stock']);

        /** @var ConstraintViolationList $violations */
        $violations = $this->validator->validate($product);
        if (count($violations) > 0) {
            throw new ConstraintViolationException((string) $violations);
        }

        return $product;
    }

    private function getReader(FormatEnumeration $format): FileReaderInterface
    {
        foreach ($this->readers as $reader) {
            if ($reader->getFormat() === $format->value) {
                return $reader;
            }
        }

        throw new \RuntimeException(sprintf("Can't find reader for format %s", $format->value));
    }

    private function getRepository(DatabaseEnumeration $database): ProductRepositoryInterface
    {
        foreach ($this->repositories as $repository) {
            if ($repository->getDatabase() === $database->value) {
                return $repository;
            }
        }

        throw new \RuntimeException(sprintf("Can't find repository for database %s", $database->value));
    }
}
