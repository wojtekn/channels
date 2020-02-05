<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model\ResourceModel\EntityMapping;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Wojtekn\CrazyCall\Model\EntityMapping;
use Wojtekn\CrazyCall\Model\ResourceModel\EntityMapping as EntityMappingResource;

/**
 * Crazy Call entities mapping collection
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
