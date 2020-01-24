<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

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
}
