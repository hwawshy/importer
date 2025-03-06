<?php

declare(strict_types=1);

namespace App\Command;

use App\Enumeration\DatabaseEnumeration;
use App\Enumeration\FormatEnumeration;
use App\Service\FeedImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:feed_import', description: 'Imports a product feed from a local file into a database')]
class FeedImportCommand extends Command
{
    public function __construct(private readonly FeedImportService $feedImportService, ?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $formats = array_map(fn (FormatEnumeration $f): string => $f->value, FormatEnumeration::cases());
        $databases = array_map(fn (DatabaseEnumeration $d): string => $d->value, DatabaseEnumeration::cases());

        $this->addArgument('filepath', InputArgument::REQUIRED, 'Path of the file to be read');
        $this->addOption(
            'format',
            null,
            InputOption::VALUE_REQUIRED,
            sprintf('Format of the file to be read. Available options are %s.', implode(',', $formats)),
            FormatEnumeration::CSV->value
        );
        $this->addOption(
            'database',
            null,
            InputOption::VALUE_REQUIRED,
            sprintf('Database to import feed into. Available options are %s.', implode(',', $databases)),
            DatabaseEnumeration::MYSQL->value
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filepath = $input->getArgument('filepath');
        $format = FormatEnumeration::tryFrom(strtolower($input->getOption('format')));
        $database = DatabaseEnumeration::tryFrom(strtolower($input->getOption('database')));

        if ($format === null) {
            throw new \InvalidArgumentException("Unknown format provided");
        }

        if ($database === null) {
            throw new \InvalidArgumentException("Unknown database type provided");
        }

        $this->feedImportService->import($filepath, $format, $database);

        return Command::SUCCESS;
    }
}
