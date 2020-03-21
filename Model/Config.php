<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * Config path constants
     */
    const CONFIG_XML_API_ADDRESS_TYPE = 'channels/connector/address_type';
    const CONFIG_XML_API_ACCOUNT = 'channels/connector/api_account';
    const CONFIG_XML_API_KEY = 'channels/connector/api_key';
    const CONFIG_XML_API_URL = 'channels/connector/api_url';
    const CONFIG_XML_API_DEBUG = 'channels/connector/debug';
    const CONFIG_XML_API_ENABLED = 'channels/connector/enabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get customer address type for phone number purpose.
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getAddressType(?int $websiteId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_XML_API_ADDRESS_TYPE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get API account
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getApiAccount(?int $websiteId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_XML_API_ACCOUNT,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get API key
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getApiKey(?int $websiteId = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_XML_API_KEY,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get API url
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getApiUrl(?int $websiteId = null): string
    {
        return rtrim($this->scopeConfig->getValue(
            self::CONFIG_XML_API_URL,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        ), '/');
    }

    /**
     * Checks if extension debug mode is enabled
     *
     * @return bool
     */
    public function isDebugEnabled(): bool
    {
        if ($this->scopeConfig->getValue(self::CONFIG_XML_API_DEBUG)) {
            return true;
        }

        return false;
    }

    /**
     * Checks if extension is enabled
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isEnabled(?int $websiteId = null): bool
    {
        if ($this->scopeConfig->getValue(
            self::CONFIG_XML_API_ENABLED,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        )) {
            return true;
        }

        return false;
    }
}
