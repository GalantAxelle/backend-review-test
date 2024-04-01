<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\GhArchiveEventRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * This command must import GitHub events.
 * You can add the parameters and code you want in this command to meet the need.
 */
class ImportGitHubEventsCommand extends Command
{
    protected static $defaultName = 'app:import-github-events';

    public function __construct(
        private GhArchiveEventRepository $eventRepository,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        //TODO: arguments ou options ?
        $this
            ->setDescription('Import GH events related to a specific keyword')
            ->addOption('date', null, InputOption::VALUE_REQUIRED, 'The date of the events you want to fetch. It should respect this format: YYYY-MM-DD')
            ->addOption('hour', null, InputOption::VALUE_REQUIRED, 'The hour of the events you want to fetch. It should respect this format: H or HH');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        ini_set('memory_limit', '4G');

        $io = new SymfonyStyle($input, $output);
        $date = $input->getOption('date');
        $hour = $input->getOption('hour');

        if (empty($date) || empty($hour) || !$this->isDateFormatCorrect($date) || !$this->isHourFormatCorrect($hour)) {
            $io->error('Both date and hour options must be provided with the correct format.');
            return Command::FAILURE;
        }

        $io->info("Fetching events for date $date and hour $hour");
        try {
            $events = $this->eventRepository->findAllWithDateAndHour($date, (int) $hour);
            $io->success('Successfully fetched events from GHArchive');
        } catch (\Exception $e) {
            $io->error('Could not retrieve events from GHArchive.');
            $io->error($e->getMessage());
        }


        return Command::SUCCESS;
    }

    private function isDateFormatCorrect(string $date): bool
    {
        try {
            $dateTime = new \DateTime($date);
            $formattedDate = $dateTime->format('Y-m-d');

            if ($formattedDate !== $date) {
                return false;
            }

            $startDate = new \DateTime('2015-01-01');
            $endDate = new \DateTime('now');

            if ($dateTime < $startDate || $dateTime > $endDate) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    private function isHourFormatCorrect(string $hour): bool
    {
        $hour = (int) $hour;

        if ($hour >= 0 && $hour <= 23) {
            return true;
        }

        return false;
    }
}
