<?php

namespace Bsi\Scheduler;

use GO\Job;
use GO\Scheduler as GoScheduler;
use RuntimeException;
use Symfony\Component\Console\Command\Command;

class Scheduler extends GoScheduler
{
    private $commandMap = [];

    public function command(string $commandClass, array $args = [], string $entrypoint = 'bin/console'): Job
    {
        if (!class_exists($commandClass) || !is_subclass_of($commandClass, Command::class)) {
            throw new RuntimeException(sprintf(
                'Class "%s" doesn\'t exist or is not a subclass of "%s"',
                $commandClass,
                Command::class
            ));
        }

        $name = $commandClass::getDefaultName();
        if ($name === null) {
            $command = new $commandClass();
            $name = $command->getName();
            trigger_error(sprintf('The class "%s" not uses static property $defaultName. Recommended set it for optimization', $commandClass), E_USER_WARNING);
        }

        $job = $this->php("$entrypoint $name", null, $args);

        $this->commandMap[$job->getId()] = $commandClass;

        return $job;
    }

    public function getCommandMap(): array
    {
        return $this->commandMap;
    }
}
