<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Wojtekn\CrazyCall\Api\Data\EntityMappingInterface;
use Wojtekn\CrazyCall\Api\Data\EntityMappingSearchResultsInterface;

/**
 * Crazy Call entities mapping repository interface.
 *
 * @api
 * @since 1.0.0
 */
interface EntityMappingRepositoryInterface
{
    /**
     * Deletes a specified entities mapping entry
     *
     * @param EntityMappingInterface $entityMapping The entities mapping
     * @return bool
     * @throws CouldNotDeleteException
     * @since 1.0.0
     */
    public function delete(EntityMappingInterface $entityMapping): bool;

    /**
     * Loads a specified entities mapping entry.
     *
     * @param int $id The entities mapping entry ID.
     * @return EntityMappingInterface Entities mapping entry interface.
     * @throws NoSuchEntityException If entities mapping isn't found.
     * @since 1.0.0
     */
    public function getById($id): EntityMappingInterface;

    /**
     * Lists entities mapping entries that match specified search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria The search criteria.
     * @return EntityMappingSearchResultsInterface Entities mapping entry search result interface.
     * @since 1.0.0
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Performs persist operations for a specified entities mapping entry.
     *
     * @param EntityMappingInterface $entityMapping The entities mapping entry.
     * @return EntityMappingInterface Entities mapping entry interface.
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @since 1.0.0
     */
    public function save(EntityMappingInterface $entityMapping);
}
