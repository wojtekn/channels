<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Model\Queue\Message;

use Magento\Framework\Model\AbstractModel;
use Wojtekn\Channels\Api\Data\CustomerExportMessageInterface;

/**
 * Message for customer export queue.
 */
class CustomerExportMessage extends AbstractModel implements CustomerExportMessageInterface
{
    /**
     * @inheritdoc
     */
    public function getCompany(): string
    {
        return (string) $this->getData(CustomerExportMessageInterface::FIELD_COMPANY);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId(): int
    {
        return (int) $this->getData(CustomerExportMessageInterface::FIELD_CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getEmail(): string
    {
        return (string) $this->getData(CustomerExportMessageInterface::FIELD_EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function getFirstName(): string
    {
        return (string) $this->getData(CustomerExportMessageInterface::FIELD_FIRST_NAME);
    }

    /**
     * @inheritdoc
     */
    public function getLastName(): string
    {
        return (string) $this->getData(CustomerExportMessageInterface::FIELD_LAST_NAME);
    }

    /**
     * @inheritdoc
     */
    public function getPhone(): string
    {
        return (string) $this->getData(CustomerExportMessageInterface::FIELD_PHONE);
    }

    /**
     * @inheritdoc
     */
    public function getWebsiteId(): int
    {
        return (int) $this->getData(CustomerExportMessageInterface::FIELD_WEBSITE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCompany(string $company)
    {
        return $this->setData(CustomerExportMessageInterface::FIELD_COMPANY, $company);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId(?int $customerId)
    {
        return $this->setData(CustomerExportMessageInterface::FIELD_CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function setEmail(string $email)
    {
        return $this->setData(CustomerExportMessageInterface::FIELD_EMAIL, $email);
    }

    /**
     * @inheritdoc
     */
    public function setFirstName(string $firstName)
    {
        return $this->setData(CustomerExportMessageInterface::FIELD_FIRST_NAME, $firstName);
    }

    /**
     * @inheritdoc
     */
    public function setLastName(string $lastName)
    {
        return $this->setData(CustomerExportMessageInterface::FIELD_LAST_NAME, $lastName);
    }

    /**
     * @inheritdoc
     */
    public function setPhone(string $phone)
    {
        return $this->setData(CustomerExportMessageInterface::FIELD_PHONE, $phone);
    }

    /**
     * @inheritdoc
     */
    public function setWebsiteId(int $websiteId)
    {
        return $this->setData(CustomerExportMessageInterface::FIELD_WEBSITE_ID, $websiteId);
    }
}
