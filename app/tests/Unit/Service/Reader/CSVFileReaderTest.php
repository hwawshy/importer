<?php

namespace App\Tests\Unit\Service\Reader;

use App\Service\FileReader\CSVFileReader;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Spatie\Snapshots\MatchesSnapshots;

class CSVFileReaderTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @dataProvider fileProvider
     */
    public function testCanReadFeedCorrectly(string $filepath): void
    {
        $reader = new CSVFileReader(new NullLogger());
        $this->assertMatchesJsonSnapshot(json_encode(iterator_to_array($reader->read($filepath))));
    }

    public function fileProvider(): iterable
    {
        yield [ __DIR__ . '/Resources/empty_feed.csv'];
        yield [__DIR__ . '/Resources/header_only.csv'];
        yield [__DIR__ . '/Resources/has_blank_line.csv'];
        yield [__DIR__ . '/Resources/mismatching_fields.csv'];
    }
}
