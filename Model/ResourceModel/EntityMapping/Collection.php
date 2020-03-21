<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Model\ResourceModel\EntityMapping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Wojtekn\Channels\Model\EntityMapping;
use Wojtekn\Channels\Model\ResourceModel\EntityMapping as EntityMappingResource;

/**
 * Channels entities mapping collection
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(EntityMapping::class, EntityMappingResource::class);
    }
}
