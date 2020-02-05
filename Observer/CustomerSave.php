<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Observer;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Wojtekn\CrazyCall\Logger\Logger;
use Wojtekn\CrazyCall\Model\Config;
use Wojtekn\CrazyCall\Model\CustomerAddressExtractor;
use Wojtekn\CrazyCall\Model\CustomerExportScheduler;

class CustomerSave implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerAddressExtractor
     */
    private $customerAddressExtractor;

    /**
     * @var CustomerExportScheduler
     */
    private $customerExportScheduler;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Config $config
     * @param CustomerAddressExtractor $customerAddressExtractor
     * @param CustomerExportScheduler $customerExportScheduler
     * @param Logger $logger
     */
    public function __construct(
        Config $config,
        CustomerAddressExtractor $customerAddressExtractor,
        CustomerExportScheduler $customerExportScheduler,
        Logger $logger
    ) {
        $this->config = $config;
        $this->customerAddressExtractor = $customerAddressExtractor;
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

        if (!$this->config->isEnabled((int) $customer->getWebsiteId())) {
            return;
        }

        if (!$this->isCustomerChanged($customer, $customerOrig)) {
            return;
        }

        $address = $this->customerAddressExtractor->getAddress($customer);
        if (!($address instanceof AddressInterface)) {
            return;
        }

        $this->logger->debug(sprintf(
            'Scheduling customer #%d from website #%d for export (CUSTOMER changed)',
            (int) $customer->getId(),
            (int) $customer->getWebsiteId()
        ));

        $this->customerExportScheduler->schedule($customer, $address);
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
        if ($customerOrig === null) {
            return false;
        }

        if ($customer->getFirstname() === $customerOrig->getFirstname() &&
            $customer->getLastname() === $customerOrig->getLastname() &&
            $customer->getEmail() === $customerOrig->getEmail()
        ) {
            return false;
        }

        return true;
    }
}
