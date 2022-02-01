<?php

namespace Bsi\Scheduler\Cli;

use Bitrix\Main\Config\Option;
use Bsi\Scheduler\Scheduler;
use Bsi\Scheduler\TaskLoader;
use Symfony\Component\Console\Command\Command;

abstract class AbstractCommand extends Command
{
    protected function createScheduler(): Scheduler
    {
        $scheduler = new Scheduler();

        $taskLoader = new TaskLoader();
        $taskLoader->load(
            $scheduler,
            $_SERVER['DOCUMENT_ROOT'] . Option::get('bsi.scheduler', 'resource_path')
        );

        return $scheduler;
    }
}
