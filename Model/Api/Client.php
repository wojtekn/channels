<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model\Api;

use Exception;
use Magento\Framework\HTTP\ZendClient;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Wojtekn\CrazyCall\Exception\ApiFailedException;
use Wojtekn\CrazyCall\Exception\ApiFailedButRetryException;
use Wojtekn\CrazyCall\Logger\Logger;
use Wojtekn\CrazyCall\Model\Config;
use Zend_Http_Client;
use Zend_Http_Client_Exception;

/**
 * Class Client
 */
class Client
{
    /**
     * Exception text which means that we should look for more detailed error in adapter
     */
    const ADAPTER_ERROR = 'Unable to read response, or response is empty';

    /**
     * @var ZendClientFactory
     */
    private $clientFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param ZendClientFactory $clientFactory
     * @param Config $config
     * @param Json $json
     * @param Logger $logger
     */
    public function __construct(
        ZendClientFactory $clientFactory,
        Config $config,
        Json $json,
        Logger $logger
    ) {
        $this->clientFactory = $clientFactory;
        $this->config = $config;
        $this->json = $json;
        $this->logger = $logger;
    }

    /**
     * Send request to the API, handle retriable and not retriable errors
     *
     * @param array $request
     * @param string $apiPath
     * @param int $websiteId
     * @return array
     * @throws ApiFailedButRetryException
     * @throws ApiFailedException
     */
    public function sendApiRequest(array $request, string $apiPath, int $websiteId): array
    {
        $apiKey = $this->config->getApiKey($websiteId);
        $apiAccount = $this->config->getApiAccount($websiteId);

        try {
            /** @var ZendClient $client */
            $client = $this->clientFactory->create();
            $client->setUri($this->config->getApiUrl() . $apiPath);
            $client->setConfig([
                'maxredirects' => 0,
                'timeout' => 30,
                'verifypeer' => false,
                'verifyhost' => false
            ]);
            $client->setHeaders('Content-Type', 'application/json');
            $client->setRawData(utf8_encode($this->json->serialize($request)));
            $client->setHeaders('Account', $apiAccount);
            $client->setHeaders('x-api-token', $apiKey);

            $response = $client->request(Zend_Http_Client::POST);
        } catch (Zend_Http_Client_Exception $e) {
            $this->logException($e, $apiPath, $client);

            if ($e->getMessage() === self::ADAPTER_ERROR && $client->getAdapter()->getError()) {
                throw new ApiFailedButRetryException(__('HTTP client adapter error: %1', $client->getAdapter()->getError()));
            } else {
                throw new ApiFailedButRetryException(__('HTTP client error: %1', $e->getMessage()));
            }
        } catch (Exception $e) {
            $this->logException($e, $apiPath, $client);
            throw new ApiFailedButRetryException(__('An unknown error occurred performing the request'), $e);
        }

        $this->logRequest($apiPath, (string) $client->getLastRequest(), (string) $client->getLastResponse());

        $this->handleApiError($response);

        return $this->parseResponse($response->getBody());
    }

    /**
     * Handle API error based on HTTP status codes.
     *
     * @param \Zend_Http_Response $response
     * @throws ApiFailedButRetryException
     * @throws ApiFailedException
     */
    private function handleApiError(\Zend_Http_Response $response): void
    {
        if ($response->getStatus() === 200) {
            return;
        }

        switch ($response->getStatus()) {
            case 401: // incorrect api key, try later
            case 500: // internal server error, try later
            case 503: // service unavailable, try later
                throw new ApiFailedButRetryException(
                    __('API returned error %1: %2', $response->getStatus(), $response->getBody())
                );
                break;
            case 400: // eg. in valid argument format
            case 406: // invalid request format
            default: // other codes
                throw new ApiFailedException(
                    __('API returned error %1: %2', $response->getStatus(), $response->getBody())
                );
                break;
        }
    }

    /**
     * Log an exception that occurred during an API request
     *
     * @param Exception $exception
     * @param string $type Request Type
     * @param Zend_Http_Client|null $client
     */
    private function logException(Exception $exception, string $type, ?Zend_Http_Client $client = null): void
    {
        $this->logger->critical($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        $this->logRequest(
            $type,
            $client !== null ? (string) $client->getLastRequest() : '',
            $client !== null ? (string) $client->getLastResponse() : ''
        );
    }

    /**
     * Log an API Request
     *
     * @param string $type
     * @param string $request
     * @param string $response
     */
    private function logRequest(string $type, string $request, string $response): void
    {
        $this->logger->debug(
            sprintf(
                'TYPE: %s' . PHP_EOL . 'REQUEST: %s' . PHP_EOL . 'RESPONSE: %s' . PHP_EOL,
                $type,
                $request,
                $response
            )
        );
    }

    /**
     * @param string $response
     * @return array
     */
    private function parseResponse(string $response): array
    {
        $response = $this->json->unserialize($response);

        if (is_array($response)) {
            return $response;
        }

        return [];
    }
}
