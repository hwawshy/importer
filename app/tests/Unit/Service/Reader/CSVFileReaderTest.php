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

    public function fileProvider(): array
    {
        return [
            'empty feed' => [__DIR__ . '/Resources/empty_feed.csv'],
            'header only' => [__DIR__ . '/Resources/header_only.csv'],
            'blank lines' => [__DIR__ . '/Resources/blank_line.csv'],
            'mismatching fields' => [__DIR__ . '/Resources/mismatching_fields.csv']
        ];
    }
}
