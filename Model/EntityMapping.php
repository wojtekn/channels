<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model;

use Magento\Framework\Model\AbstractModel;
use Wojtekn\CrazyCall\Api\Data\EntityMappingInterface;
use Wojtekn\CrazyCall\Model\ResourceModel\EntityMapping as EntityMappingResource;

/**
 * Model for Crazy Call entities mapping
 */
class EntityMapping extends AbstractModel implements EntityMappingInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(EntityMappingResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getExternalId(): int
    {
        return (int) $this->getData(EntityMappingInterface::FIELD_EXTERNAL_ID);
    }

    /**
     * @inheritdoc
     */
    public function getInternalId(): int
    {
        return (int) $this->getData(EntityMappingInterface::FIELD_INTERNAL_ID);
    }

    /**
     * @inheritdoc
     */
    public function getObjectType(): string
    {
        return $this->getData(EntityMappingInterface::FIELD_OBJECT_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setExternalId(int $externalId)
    {
        return $this->setData(EntityMappingInterface::FIELD_EXTERNAL_ID, $externalId);
    }

    /**
     * @inheritdoc
     */
    public function setInternalId(int $internalId)
    {
        return $this->setData(EntityMappingInterface::FIELD_INTERNAL_ID, $internalId);
    }

    /**
     * @inheritdoc
     */
    public function setObjectType(string $objectType)
    {
        return $this->setData(EntityMappingInterface::FIELD_OBJECT_TYPE, $objectType);
    }
}
