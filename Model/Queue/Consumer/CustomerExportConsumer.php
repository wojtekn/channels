<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Model\Queue\Consumer;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Wojtekn\Channels\Api\Data\CustomerExportMessageInterface;
use Wojtekn\Channels\Api\Data\EntityMappingInterface;
use Wojtekn\Channels\Api\Data\EntityMappingInterfaceFactory;
use Wojtekn\Channels\Api\EntityMappingRepositoryInterface;
use Wojtekn\Channels\Exception\ApiFailedButRetryException;
use Wojtekn\Channels\Exception\ApiFailedException;
use Wojtekn\Channels\Logger\Logger;
use Wojtekn\Channels\Model\Api\Request\CustomerExport as CustomerExportRequest;
use Wojtekn\Channels\Model\Api\Request\CustomerExportFactory as CustomerExportRequestFactory;
use Wojtekn\Channels\Model\Config;
use Wojtekn\Channels\Model\Queue\TopicRegistry;

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
     * @var EntityMappingInterfaceFactory
     */
    private $entityMappingFactory;

    /**
     * @var EntityMappingRepositoryInterface
     */
    private $entityMappingRepository;

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
     * @param EntityMappingInterfaceFactory $entityMappingFactory
     * @param EntityMappingRepositoryInterface $entityMappingRepository
     * @param Logger $logger
     * @param PublisherInterface $publisher
     */
    public function __construct(
        Config $config,
        CustomerExportRequestFactory $customerExportRequestFactory,
        EntityMappingInterfaceFactory $entityMappingFactory,
        EntityMappingRepositoryInterface $entityMappingRepository,
        Logger $logger,
        PublisherInterface $publisher
    ) {
        $this->config = $config;
        $this->customerExportRequestFactory = $customerExportRequestFactory;
        $this->entityMappingFactory = $entityMappingFactory;
        $this->entityMappingRepository = $entityMappingRepository;
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

            $entityMapping = $this->entityMappingFactory->create();
            $entityMapping->setObjectType(EntityMappingInterface::TYPE_CUSTOMER);
            $entityMapping->setInternalId($message->getCustomerId());
            $entityMapping->setExternalId($response['id']);

            try {
                $this->entityMappingRepository->save($entityMapping);
                // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch,Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
            } catch (AlreadyExistsException | CouldNotSaveException $exception) {
                // intentionally omitted - it means that mapping for this customer already exists
            }

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
