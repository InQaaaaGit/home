<?php

namespace Psr\Log;

use moodle\admin\tool\pluginskel\vendor\psr\log\Psr\Log\LoggerInterface;

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface|null
     */
    protected ?LoggerInterface $logger = null;

    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
