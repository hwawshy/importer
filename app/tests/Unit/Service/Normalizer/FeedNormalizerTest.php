<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Normalizer;

use App\Exception\FeedNormalizerException;
use App\Service\Normalizer\FeedNormalizer;
use PHPUnit\Framework\TestCase;

class FeedNormalizerTest extends TestCase
{
    public function testCanNormalizeFieldsCorrectly(): void
    {
        $row = [
            'gtin' => '4990528354555',
            'language' => 'fr',
            'title' => 'Product 1',
            'picture' => 'https://example.com/picture1.png',
            'description' => 'This is some description.',
            'price' => '69.69 Euros',
            'stock' => '9001'
        ];

        $normalizer = new FeedNormalizer();
        $normalized = $normalizer->normalizeRow($row);

        $this->assertSame('04990528354555', $normalized['gtin']); // left padded with a zero
        $this->assertSame($row['language'], $normalized['language']);
        $this->assertSame($row['title'], $normalized['title']);
        $this->assertSame($row['picture'], $normalized['picture']);
        $this->assertSame($row['description'], $normalized['description']);
        $this->assertSame(6969, $normalized['price']);
        $this->assertSame(9001, $normalized['stock']);
    }

    /**
     * @dataProvider InvalidRowProvider
     */
    public function testThrowsExceptionOnInvalidRow(array $row): void
    {
        $this->expectException(FeedNormalizerException::class);
        $normalizer = new FeedNormalizer();
        $normalizer->normalizeRow($row);
    }

    public function InvalidRowProvider(): array
    {
        return [
            'missing fields' => [[
                // price missing
                'gtin' => '4990528354555',
                'language' => 'fr',
                'title' => 'Product 1',
                'picture' => 'https://example.com/picture1.png',
                'description' => 'This is some description.',
                'stock' => '9001'
            ]],
            'invalid GTIN' => [[
                'gtin' => '4990528354555123', // too long
                'language' => 'fr',
                'title' => 'Product 1',
                'picture' => 'https://example.com/picture1.png',
                'description' => 'This is some description.',
                'price' => '69.69',
                'stock' => '9001'
            ]],
            'invalid price' => [[
                'gtin' => '4990528354555',
                'language' => 'fr',
                'title' => 'Product 1',
                'picture' => 'https://example.com/picture1.png',
                'description' => 'This is some description.',
                'price' => 'invalid', // not numeric
                'stock' => '9001'
            ]],
            'invalid stock' => [[
                'gtin' => '4990528354555',
                'language' => 'fr',
                'title' => 'Product 1',
                'picture' => 'https://example.com/picture1.png',
                'description' => 'This is some description.',
                'price' => '69.69',
                'stock' => 'rubbish' // not numeric
            ]]
        ];
    }
}
