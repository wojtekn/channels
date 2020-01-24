<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Observer;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Wojtekn\CrazyCall\Model\Config;
use Wojtekn\CrazyCall\Model\CustomerExportScheduler;

class CustomerSave implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerExportScheduler
     */
    private $customerExportScheduler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Config $config
     * @param CustomerExportScheduler $customerExportScheduler
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        CustomerExportScheduler $customerExportScheduler,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->customerExportScheduler = $customerExportScheduler;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer): void
    {
        $customer = $observer->getCustomerDataObject();
        $customerOrig = $observer->getOrigCustomerDataObject();
        if (!$this->isCustomerChanged($customer, $customerOrig)) {
            return;
        }

        $address = $this->getAddress($customer);
        if (!$this->isAddressValid($address)) {
            return;
        }

        $this->logger->info(sprintf(
            'Scheduling customer #%d for export (CUSTOMER changed)',
            (int) $customer->getId()
        ));

        $this->customerExportScheduler->schedule($customer, $address);
    }

    /**
     * Get customer's default billing or shipping address, based on configured value.
     *
     * @param CustomerInterface $customer
     * @return AddressInterface|null
     */
    private function getAddress(CustomerInterface $customer): ?AddressInterface
    {
        $type = $this->config->getAddressType((int) $customer->getWebsiteId());

        foreach ($customer->getAddresses() as $address) {
            if (
                ($type == AbstractAddress::TYPE_BILLING && $address->isDefaultBilling()) ||
                ($type == AbstractAddress::TYPE_SHIPPING && $address->isDefaultShipping())
            ) {
                return $address;
            }
        }

        return null;
    }

    /**
     * Return true if customer has default billing / default shipping address
     *
     * @param AddressInterface|null $address
     * @return bool
     */
    private function isAddressValid(?AddressInterface $address): bool
    {
        if ($address instanceof AddressInterface) {
            return true;
        }

        return false;
    }

    /**
     * Return true if customer data was changed.
     *
     * $customerOrig is empty when customer is being registered for the first time and does not have address
     * if it is registration after guest checkout. Address afterSave plugin will catch this case.
     *
     * In terms of changing customer data, it checks for following keys:
     * - firstname
     * - lastname
     * - email
     *
     * @param CustomerInterface $customer
     * @param CustomerInterface|null $customerOrig
     * @return bool
     */
    private function isCustomerChanged(CustomerInterface $customer, ?CustomerInterface $customerOrig): bool
    {
        if (is_null($customerOrig)) {
            return false;
        }

        if (
            $customer->getFirstname() === $customerOrig->getFirstname() &&
            $customer->getLastname() === $customerOrig->getLastname() &&
            $customer->getEmail() === $customerOrig->getEmail()
        ) {
            return false;
        }

        return true;
    }
}
