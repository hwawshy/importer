<?php

declare(strict_types=1);

namespace App\Service\FileReader;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.file_reader')]
interface FileReaderInterface
{
    /** @return iterable<Array<string, string>> */
    public function read(string $filepath): iterable;

    public function getFormat(): string;
}
