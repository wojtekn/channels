<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Crazy Call entities mapping search result interface.
 *
 * @api
 * @since 1.0.0
 */
interface EntityMappingSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return EntityMappingInterface[] Array of collection items.
     * @since 1.0.0
     */
    public function getItems();

    /**
     * Sets collection items.
     *
     * @param EntityMappingInterface[] $items
     * @return $this
     * @since 1.0.0
     */
    public function setItems(array $items);
}
