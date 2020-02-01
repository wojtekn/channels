<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model\Queue\Consumer;

use Magento\Framework\MessageQueue\PublisherInterface;
use Wojtekn\CrazyCall\Api\Data\CustomerExportMessageInterface;
use Wojtekn\CrazyCall\Exception\ApiFailedButRetryException;
use Wojtekn\CrazyCall\Exception\ApiFailedException;
use Wojtekn\CrazyCall\Logger\Logger;
use Wojtekn\CrazyCall\Model\Api\Request\CustomerExport as CustomerExportRequest;
use Wojtekn\CrazyCall\Model\Api\Request\CustomerExportFactory as CustomerExportRequestFactory;
use Wojtekn\CrazyCall\Model\Config;
use Wojtekn\CrazyCall\Model\Queue\TopicRegistry;

/**
 * Consumer for export message.
 */
class CustomerExportConsumer
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerExportRequestFactory
     */
    private $customerExportRequestFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @param Config $config
     * @param CustomerExportRequestFactory $customerExportRequestFactory
     * @param Logger $logger
     * @param PublisherInterface $publisher
     */
    public function __construct(
        Config $config,
        CustomerExportRequestFactory $customerExportRequestFactory,
        Logger $logger,
        PublisherInterface $publisher
    ) {
        $this->config = $config;
        $this->customerExportRequestFactory = $customerExportRequestFactory;
        $this->logger = $logger;
        $this->publisher = $publisher;
    }

    /**
     * Process message
     *
     * @param CustomerExportMessageInterface $message
     * @throws ApiFailedException
     */
    public function process(CustomerExportMessageInterface $message)
    {
        try {
            /** @var CustomerExportRequest $customerExportRequest */
            $customerExportRequest = $this->customerExportRequestFactory->create();
            $response = $customerExportRequest->send($message);

            // @todo get id from $response here and add mapping to db table

            $this->logger->debug(sprintf('Job finished successfully.'));
        } catch (ApiFailedException $exception) {
            $this->logger->critical(sprintf('Job failed: %s', $exception->getMessage()));
            throw $exception;
        } catch (ApiFailedButRetryException $exception) {
            $this->logger->critical(sprintf('Job failed (will be retried): %s', $exception->getMessage()));
            $this->publisher->publish(
                TopicRegistry::TOPIC_CUSTOMER_EXPORT,
                $message
            );
        }
    }
}
