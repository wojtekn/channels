<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Model;

use Magento\Framework\Model\AbstractModel;
use Wojtekn\Channels\Api\Data\EntityMappingInterface;
use Wojtekn\Channels\Model\ResourceModel\EntityMapping as EntityMappingResource;

/**
 * Model for Channels entities mapping
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
    public function getObjectType(): int
    {
        return (int) $this->getData(EntityMappingInterface::FIELD_OBJECT_TYPE);
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
    public function setObjectType(int $objectType)
    {
        return $this->setData(EntityMappingInterface::FIELD_OBJECT_TYPE, $objectType);
    }
}
