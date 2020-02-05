<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model;

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Address\AbstractAddress;

class CustomerAddressExtractor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get customer's default billing or shipping address, based on configured value.
     *
     * @param CustomerInterface $customer
     * @return AddressInterface|null
     */
    public function getAddress(CustomerInterface $customer): ?AddressInterface
    {
        $type = $this->config->getAddressType((int) $customer->getWebsiteId());

        foreach ($customer->getAddresses() as $address) {
            if (($type == AbstractAddress::TYPE_BILLING && $address->isDefaultBilling()) ||
                ($type == AbstractAddress::TYPE_SHIPPING && $address->isDefaultShipping())
            ) {
                return $address;
            }
        }

        return null;
    }
}
