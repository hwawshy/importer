<?php

declare(strict_types=1);

namespace App\Service\FileReader;

use App\Enumeration\FormatEnumeration;
use Psr\Log\LoggerInterface;

readonly class CSVFileReader implements FileReaderInterface
{
    public function __construct(private LoggerInterface $logger) {
    }

    private const int CHUNK_SIZE = 20;

    public function read(string $filepath): iterable
    {
        $handle = fopen($filepath, 'r');
        if ($handle === false) {
            throw new \RuntimeException(sprintf("Can't open %s", $filepath));
        }

        /** @var ?array $header */
        $header = null;
        $rows = [];
        $line = 0;

        try {
            while ($row = fgetcsv($handle, escape: "")) {
                $line++;
                if ($row === [null]) {
                    // blank line
                    continue;
                }

                if ($header === null) {
                    $header = $row;
                    continue;
                }

                if (count($header) !== count($row)) {
                    $this->logger->warning(sprintf('Line %d in %s has a mismatching number of fields. Skipping...', $line, $filepath));
                    continue;
                }

                $rows[] = array_combine($header, $row);

                if (count($rows) === self::CHUNK_SIZE) {
                    yield from $rows;
                    $rows = [];
                }
            }

            if (!feof($handle)) {
                throw new \RuntimeException(sprintf("Unexpected error while reading %s", $filepath));
            }

            yield from $rows;
        } finally {
            fclose($handle);
        }
    }

    public function getFormat(): string
    {
        return FormatEnumeration::CSV->value;
    }
}
