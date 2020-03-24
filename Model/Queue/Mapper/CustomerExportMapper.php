<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Model\Queue\Mapper;

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
     * @var EntityMappingRepositoryInterface
     */
    private $entityMappingRepository;

    /**
     * @param EntityMappingRepositoryInterface $entityMappingRepository
     */
    public function __construct(
        EntityMappingRepositoryInterface $entityMappingRepository
    ) {
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
            'details' => [
                'firstName' => $message->getFirstName(),
                'lastName' => $message->getLastName(),
                'email' => $message->getEmail(),
                'mainMsisdn' => $message->getPhone(),
                'company' => $message->getCompany()
            ]
        ];
    }
}
