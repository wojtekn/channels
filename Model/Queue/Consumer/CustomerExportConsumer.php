<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model\Queue\Consumer;

use Psr\Log\LoggerInterface;
use Wojtekn\CrazyCall\Api\Data\CustomerExportMessageInterface;

/**
 * Consumer for export message.
 */
class CustomerExportConsumer
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Process message
     *
     * @param CustomerExportMessageInterface $message
     * @return void
     */
    public function process(CustomerExportMessageInterface $message)
    {
        $this->logger->critical('CrazyCall customer export consumer - to be implemented.');
        return;
    }
}
