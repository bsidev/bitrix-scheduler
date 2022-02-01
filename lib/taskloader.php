<?php

namespace Bsi\Scheduler;

use Bsi\Scheduler\Exception\LoaderException;

class TaskLoader
{
    public function load(Scheduler $scheduler, string $path): void
    {
        if (!file_exists($path) || !is_file($path)) {
            throw new LoaderException(sprintf('Unable to find file "%s".', $path));
        }

        $callback = include $path;
        if (is_object($callback) && is_callable($callback)) {
            $callback($scheduler);
        } else {
            throw new LoaderException(sprintf('Unable to load file "%s".', $path));
        }
    }
}
