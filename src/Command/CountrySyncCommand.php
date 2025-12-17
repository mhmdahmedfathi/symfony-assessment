<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\CountryService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:country:sync',
    description: 'Syncs country data from REST Countries API',
)]
class CountrySyncCommand extends Command
{
    public function __construct(
        private CountryService $countryService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Starting Country Synchronization');

        try {
            $this->countryService->syncCountries();
            $io->success('Country synchronization completed successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Synchronization failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}