<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Plugin;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\AddressRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Wojtekn\Channels\Logger\Logger;
use Wojtekn\Channels\Model\Config;
use Wojtekn\Channels\Model\CustomerExportScheduler;

class AddressSave
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
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Config $config
     * @param CustomerExportScheduler $customerExportScheduler
     * @param CustomerRegistry $customerRegistry
     * @param Logger $logger
     */
    public function __construct(
        Config $config,
        CustomerExportScheduler $customerExportScheduler,
        CustomerRegistry $customerRegistry,
        Logger $logger
    ) {
        $this->config = $config;
        $this->customerExportScheduler = $customerExportScheduler;
        $this->customerRegistry = $customerRegistry;
        $this->logger = $logger;
    }

    /**
     * Schedule customer synchronization after address was saved
     *
     * @see \Magento\Customer\Model\ResourceModel\AddressRepository::save()
     *
     * @param AddressRepository $addressRepository
     * @param AddressInterface $address
     * @param AddressInterface $addressToSave
     *
     * @return AddressInterface
     */
    public function afterSave(
        AddressRepository $addressRepository,
        AddressInterface $address,
        AddressInterface $addressToSave
    ) {
        try {
            $customer = $this->customerRegistry->retrieve($address->getCustomerId());
            $customer = $customer->getDataModel();
        } catch (NoSuchEntityException $exception) {
            return $address;
        }

        if (!$this->config->isEnabled((int) $customer->getWebsiteId())) {
            return $address;
        }

        if (!$this->isAddressValid((int) $customer->getWebsiteId(), $addressToSave)) {
            return $address;
        }

        $this->logger->debug(sprintf(
            'Scheduling customer #%d from website #%d for export (ADDRESS changed)',
            (int) $customer->getId(),
            (int) $customer->getWebsiteId()
        ));

        $this->customerExportScheduler->schedule($customer, $address);

        return $address;
    }

    /**
     * Return true if address is a default billing / default shipping address
     *
     * We are using address from param provided to repository save() method instead of one returned by
     * the method because the latter doesn't include default_billing and default_shipping keys.
     *
     * @param int $websiteId
     * @param AddressInterface $address
     * @return bool
     */
    private function isAddressValid(int $websiteId, AddressInterface $address): bool
    {
        $type = $this->config->getAddressType($websiteId);

        if (($type == AbstractAddress::TYPE_BILLING && !$address->isDefaultBilling()) ||
            ($type == AbstractAddress::TYPE_SHIPPING && !$address->isDefaultShipping())
        ) {
            return false;
        }

        return true;
    }
}
