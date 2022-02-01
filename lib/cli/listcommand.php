<?php

namespace Bsi\Scheduler\Cli;

use Cron\CronExpression;
use GO\Job;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends AbstractCommand
{
    protected static $defaultName = 'schedule:list';

    protected function configure(): void
    {
        $this->setDescription('Displays the list of scheduled tasks.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $scheduler = $this->createScheduler();

        $table = new Table($output);
        $table->setHeaders(
            [
                '#',
                'Task',
                'Expression',
                'Command to Run',
            ]
        );
        $row = 0;

        $commandMap = $scheduler->getCommandMap();

        /** @var Job $job */
        foreach ($scheduler->getQueuedJobs() as $i => $job) {
            $description = '';
            $commandClass = $commandMap[$job->getId()] ?? null;
            if ($commandClass) {
                $consoleCommand = new $commandClass();
                $description = $consoleCommand->getDescription();
                unset($consoleCommand);
            }
            if (!$description) {
                $description = '#' . ($i + 1);
            }

            $reflectionClass = new \ReflectionClass($job);

            $command = $reflectionClass->getProperty('command');
            $command->setAccessible(true);
            $command = $command->getValue($job);

            $executionTime = $reflectionClass->getProperty('executionTime');
            $executionTime->setAccessible(true);
            /** @var CronExpression $expression */
            $expression = $executionTime->getValue($job);

            if (is_callable($command)) {
                $compiled = 'function';
            } else {
                $compiled = $job->compile();
            }

            $table->addRow([
                ++$row,
                $description,
                $expression->getExpression(),
                $compiled,
            ]);
        }

        $table->render();

        return 0;
    }
}
