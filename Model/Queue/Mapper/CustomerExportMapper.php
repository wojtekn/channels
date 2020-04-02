<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Model\Queue\Mapper;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Wojtekn\Channels\Api\Data\CustomerExportMessageInterface;
use Wojtekn\Channels\Api\Data\EntityMappingInterface;
use Wojtekn\Channels\Api\EntityMappingRepositoryInterface;

/**
 * Mapper for export message.
 */
class CustomerExportMapper
{
    /**
     * @var UrlInterface
     */
    private $backendUrl;

    /**
     * @var EntityMappingRepositoryInterface
     */
    private $entityMappingRepository;

    /**
     * @param UrlInterface $backendUrl
     * @param EntityMappingRepositoryInterface $entityMappingRepository
     */
    public function __construct(
        UrlInterface $backendUrl,
        EntityMappingRepositoryInterface $entityMappingRepository
    ) {
        $this->backendUrl = $backendUrl;
        $this->entityMappingRepository = $entityMappingRepository;
    }

    /**
     * Maps queue message to API request.
     *
     * @param CustomerExportMessageInterface $message
     * @return array
     */
    public function map(CustomerExportMessageInterface $message): array
    {
        try {
            $existingContactId = $this->entityMappingRepository->getByInternalId(
                $message->getCustomerId(),
                EntityMappingInterface::TYPE_CUSTOMER
            );
        } catch (NoSuchEntityException $exception) {
            $existingContactId = null;
        }

        return [
            'existingContactId' => $existingContactId,
            'firstNameColumnName' => 'firstName',
            'lastNameColumnName' => 'lastName',
            'emailColumnName' => 'email',
            'companyColumnName' => 'company',
            'alternativeMsisdnColumnNames'  => ['mainMsisdn'],
            'tagsColumnNames' => ['tag'],
            'externalLinkColumnName' => 'externalLink',
            'details' => [
                'firstName' => $message->getFirstName(),
                'lastName' => $message->getLastName(),
                'email' => $message->getEmail(),
                'mainMsisdn' => $message->getPhone(),
                'company' => $message->getCompany(),
                'externalLink' => $this->getCustomerEditUrl($message->getCustomerId()),
                'tag' => 'Magento',
            ]
        ];
    }

    /**
     * Generate link pointing to customer edit form in the backend.
     *
     * @param int $customerId
     * @return string
     */
    private function getCustomerEditUrl(int $customerId): string
    {
        return $this->backendUrl->getUrl(
            'customer/index/edit',
            ['id' => $customerId, '_nosecret' => true]
        );
    }
}
