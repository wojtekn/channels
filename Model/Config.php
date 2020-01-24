<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * Config path constants
     */
    const CONFIG_XML_API_ADDRESS_TYPE = 'crazycall/connector/address_type';
    const CONFIG_XML_API_ACCOUNT = 'crazycall/connector/api_account';
    const CONFIG_XML_API_KEY = 'crazycall/connector/api_key';
    const CONFIG_XML_API_URL = 'crazycall/connector/api_url';
    const CONFIG_XML_API_ENABLED = 'crazycall/connector/enabled';

    /**
     * @var array
     */
    private $apiUrls = [
        'import_contact' => 'api/v1/contacts'
    ];

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
     * @param string $type
     * @param int|null $websiteId
     * @return string
     */
    public function getApiUrl(string $type, ?int $websiteId = null): string
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_XML_API_URL,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        ) . $this->apiUrls[$type];
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
