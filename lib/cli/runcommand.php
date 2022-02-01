<?php

namespace Bsi\Scheduler\Cli;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends AbstractCommand
{
    protected static $defaultName = 'schedule:run';

    protected function configure(): void
    {
        $this->setDescription('Runs the scheduled tasks.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $scheduler = $this->createScheduler();
        $scheduler->run();

        return 0;
    }
}
