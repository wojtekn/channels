<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Api\Data;

/**
 * Interface for Crazy Call entities mapping data object
 */
interface EntityMappingInterface
{
    /**
     * Constants for database field names.
     */
    const FIELD_EXTERNAL_ID = 'external_id';
    const FIELD_ID = 'id';
    const FIELD_INTERNAL_ID = 'internal_id';
    const FIELD_OBJECT_TYPE = 'object_type';

    /**
     * Return the external id of mapping entry
     *
     * @return int
     */
    public function getExternalId(): int;

    /**
     * Return the internal id of mapping entry
     *
     * @return int
     */
    public function getInternalId(): int;

    /**
     * Return the object type of mapping entry
     *
     * @return string
     */
    public function getObjectType(): string;

    /**
     * Set the id of the external
     *
     * @param int $externalId
     * @return $this
     */
    public function setExternalId(int $externalId);

    /**
     * Set the id of the internal
     *
     * @param int $internalId
     * @return $this
     */
    public function setInternalId(int $internalId);

    /**
     * Set the object type of mapping entry
     *
     * @param string $objectType
     * @return $this
     */
    public function setObjectType(string $objectType);
}
