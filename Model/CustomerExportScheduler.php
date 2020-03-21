<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Model;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Wojtekn\Channels\Api\Data\CustomerExportInterfaceFactory;
use Wojtekn\Channels\Api\Data\CustomerExportMessageInterfaceFactory;
use Wojtekn\Channels\Logger\Logger;

class CustomerExportScheduler
{
    /**
     * @var CustomerExportMessageInterfaceFactory
     */
    private $customerExportMessageFactory;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * Array of scheduled customers
     *
     * @var array
     */
    private static $scheduledCustomers = [];

    /**
     * @param CustomerExportMessageInterfaceFactory $customerExportMessageFactory
     * @param PublisherInterface $publisher
     * @param Logger $logger
     */
    public function __construct(
        CustomerExportMessageInterfaceFactory $customerExportMessageFactory,
        PublisherInterface $publisher,
        Logger $logger
    ) {
        $this->customerExportMessageFactory = $customerExportMessageFactory;
        $this->publisher = $publisher;
        $this->logger = $logger;
    }

    /**
     * @param CustomerInterface $customer
     * @param AddressInterface $address
     * @return bool
     */
    public function schedule(CustomerInterface $customer, AddressInterface $address): bool
    {
        $customerId = (int) $customer->getId();
        if (isset(self::$scheduledCustomers[$customerId])) {
            return false;
        }
        self::$scheduledCustomers[$customerId] = true;

        /** @var CustomerExportMessageInterfaceFactory $customerExportMessage */
        $customerExportMessage = $this->customerExportMessageFactory->create();

        $customerExportMessage->setCustomerId((int) $customer->getId());
        $customerExportMessage->setFirstName((string) $customer->getFirstname());
        $customerExportMessage->setLastName((string) $customer->getLastname());
        $customerExportMessage->setEmail((string) $customer->getEmail());
        $customerExportMessage->setWebsiteId((int) $customer->getWebsiteId());

        $customerExportMessage->setPhone((string) $address->getTelephone());
        $customerExportMessage->setCompany((string) $address->getCompany());

        try {
            $this->publisher->publish('channels.customer.export', $customerExportMessage);
            return true;
        } catch (\Exception $exception) {
            $this->logger->error(sprintf(
                'Error occurred when scheduling customer #%d for export: "%s"',
                (int) $customer->getId(),
                $exception->getMessage()
            ));
            return false;
        }
    }
}
