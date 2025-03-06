<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use App\Exception\FeedNormalizerException;

readonly class FeedNormalizer
{
    private const array REQUIRED_FIELDS = ['gtin', 'language', 'title', 'picture', 'description', 'price', 'stock'];

    /** @param array<string, string> $row
     * @throws FeedNormalizerException
     */
    public function normalizeRow(array $row): array
    {
        if (array_any(self::REQUIRED_FIELDS, fn($field) => !array_key_exists($field, $row))) {
            throw new FeedNormalizerException('Row missing required field(s)');
        }

        $row['gtin'] = $this->normalizeGTIN($row['gtin']);
        $row['price'] = $this->normalizePrice($row['price']);
        $row['stock'] = $this->normalizeStock($row['stock']);

        return $row;
    }

    private function normalizeGTIN(string $gtin): string
    {
        // Simple check, ignore check digit
        if (!is_numeric($gtin) || strlen($gtin) > 14) {
            throw new FeedNormalizerException(sprintf('Invalid GTIN %s', $gtin));
        }

        return str_pad($gtin, 14, "0", STR_PAD_LEFT);
    }

    private function normalizePrice(string $price): int
    {
        if (!$normalized = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) {
            throw new FeedNormalizerException(sprintf('Invalid price %s', $price));
        }

        return intval(round(floatval($normalized), 2) * 100);
    }

    private function normalizeStock(string $stock): int
    {
        if (!$normalized = filter_var($stock, FILTER_SANITIZE_NUMBER_INT)) {
            throw new FeedNormalizerException(sprintf('Invalid stock %s', $stock));
        }

        return (int) $normalized;
    }
}
