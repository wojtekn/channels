<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

/**
 * Crazy Call entities mapping Resource Model
 */
class EntityMapping extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('crazycall_mapping', 'id');
    }
}
