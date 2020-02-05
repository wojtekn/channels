<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Logger\Handler;

use Exception;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;
use Wojtekn\CrazyCall\Model\Config;

/**
 * Write CrazyCall related messages to a specific log
 */
class CrazyCall extends Base
{
    /**
     * @var string Name of the log file
     */
    protected $fileName = '/var/log/crazy-call-debug.log';

    /**
     * Log level to log
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param DriverInterface $filesystem
     * @param Config $config
     * @param string $filePath
     * @param string $fileName
     * @throws Exception
     */
    public function __construct(
        DriverInterface $filesystem,
        Config $config,
        $filePath = null,
        $fileName = null
    ) {
        $this->config = $config;
        parent::__construct($filesystem, $filePath, $fileName);
    }

    /**
     * Extends handle method to ignore debug messages if debug mode is disabled.
     *
     * @param array $record
     * @return bool
     */
    public function handle(array $record)
    {
        if (!$this->isDebugEnabled() && $this->isDebug($record)) {
            return true;
        }

        return parent::handle($record);
    }

    /**
     * Checks if debug mode is enabled for the extension.
     *
     * @return bool
     */
    private function isDebugEnabled(): bool
    {
        if ($this->config->isDebugEnabled()) {
            return true;
        }

        return false;
    }

    /**
     * Checks if current message has level DEBUG (100)
     *
     * @param $record
     * @return bool
     */
    private function isDebug($record): bool
    {
        if ($record['level'] === Logger::DEBUG) {
            return true;
        }

        return false;
    }
}
