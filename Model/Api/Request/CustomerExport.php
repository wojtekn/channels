<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model\Api\Request;

use Magento\Framework\Serialize\Serializer\Json;
use Wojtekn\CrazyCall\Api\Data\CustomerExportMessageInterface;
use Wojtekn\CrazyCall\Exception\ApiFailedButRetryException;
use Wojtekn\CrazyCall\Exception\ApiFailedException;
use Wojtekn\CrazyCall\Model\Api\Client;
use Wojtekn\CrazyCall\Model\Config;
use Wojtekn\CrazyCall\Model\Queue\Mapper\CustomerExportMapper;

/**
 * Class CustomerExport
 *
 * Handles request to export customer to service.
 */
class CustomerExport
{
    /**
     * Request type
     */
    const API_PATH = '/api/v1/contacts';

    /**
     * @var Client
     */
    private $apiClient;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerExportMapper
     */
    private $customerExportMapper;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param Client $apiClient
     * @param Config $config
     * @param CustomerExportMapper $customerExportMapper
     * @param Json $json
     */
    public function __construct(
        Client $apiClient,
        Config $config,
        CustomerExportMapper $customerExportMapper,
        Json $json
    ) {
        $this->apiClient = $apiClient;
        $this->config = $config;
        $this->customerExportMapper = $customerExportMapper;
        $this->json = $json;
    }

    /**
     * Sends API request using Api Client
     *
     * @param CustomerExportMessageInterface $message
     * @return array
     * @throws ApiFailedButRetryException
     * @throws ApiFailedException
     */
    public function send(
        CustomerExportMessageInterface $message
    ): array {
        $requestData = $this->customerExportMapper->map($message);

        $response = $this->apiClient->sendApiRequest(
            $requestData,
            self::API_PATH,
            $message->getWebsiteId()
        );

        if (!isset($response['id'])) {
            throw new ApiFailedButRetryException(__('Response does not contains valid contact record (id missing)'));
        }

        return $response;
    }
}
