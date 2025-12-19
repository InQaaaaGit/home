<?php

namespace Psr\Log;

use moodle\admin\tool\pluginskel\vendor\psr\log\Psr\Log\LoggerInterface;

/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void;
}
