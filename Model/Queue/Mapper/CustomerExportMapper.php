<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model\Queue\Mapper;

use Wojtekn\CrazyCall\Api\Data\CustomerExportMessageInterface;

/**
 * Mapper for export message.
 */
class CustomerExportMapper
{
    /**
     * Maps queue message to API request.
     *
     * @param CustomerExportMessageInterface $message
     * @return array
     */
    public function map(CustomerExportMessageInterface $message): array
    {
        return [
            'projectId' => 1,
            'mainMsisdnColumnName' => 'mainMsisdn',
            'firstNameColumnName' => 'firstName',
            'lastNameColumnName' => 'lastName',
            'emailColumnName' => 'email',
            'companyColumnName' => 'company',
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
