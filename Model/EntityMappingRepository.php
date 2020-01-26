<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Wojtekn\CrazyCall\Api\Data\EntityMappingInterface;
use Wojtekn\CrazyCall\Api\Data\EntityMappingInterfaceFactory;
use Wojtekn\CrazyCall\Api\Data\EntityMappingSearchResultsInterface;
use Wojtekn\CrazyCall\Api\Data\EntityMappingSearchResultsInterfaceFactory;
use Wojtekn\CrazyCall\Api\EntityMappingRepositoryInterface;
use Wojtekn\CrazyCall\Model\ResourceModel\EntityMapping as EntityMappingResource;
use Wojtekn\CrazyCall\Model\ResourceModel\EntityMapping\Collection as EntityMappingCollection;
use Wojtekn\CrazyCall\Model\ResourceModel\EntityMapping\CollectionFactory as EntityMappingCollectionFactory;

/**
 * Crazy Call entities mapping repository
 */
class EntityMappingRepository implements EntityMappingRepositoryInterface
{
    /**
     * @var EntityMappingCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var EntityMappingInterfaceFactory
     */
    private $entityMappingFactory;

    /**
     * @var EntityMappingResource
     */
    private $entityMappingResource;

    /**
     * @var EntityMappingSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @param EntityMappingCollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param EntityMappingInterfaceFactory $entityMappingFactory
     * @param EntityMappingResource $entityMappingResource
     * @param EntityMappingSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        EntityMappingCollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        EntityMappingInterfaceFactory $entityMappingFactory,
        EntityMappingResource $entityMappingResource,
        EntityMappingSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->entityMappingFactory = $entityMappingFactory;
        $this->entityMappingResource = $entityMappingResource;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var EntityMappingCollection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var EntityMappingSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        // Collections return last page of results no matter how high the
        // page gets. This ensures nothing is returned if we're beyond the last page.
        $page = $searchCriteria->getCurrentPage();
        if ($page && $collection->getLastPageNumber() < $page) {
            $searchResults->setItems([]);
            $searchResults->setTotalCount(0);
            return $searchResults;
        }

        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getById($id): EntityMappingInterface
    {
        $entityMappingModel = $this->entityMappingFactory->create();

        $this->entityMappingResource->load($entityMappingModel, $id);

        if (!$entityMappingModel->getId()) {
            throw new NoSuchEntityException(__('No Crazy Call entities mapping entry with ID %1', $id));
        }

        return $entityMappingModel;
    }

    /**
     * @inheritdoc
     */
    public function delete(EntityMappingInterface $entityMapping): bool
    {
        try {
            $this->entityMappingResource->delete($entityMapping);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function save(EntityMappingInterface $entityMapping)
    {
        try {
            $this->entityMappingResource->save($entityMapping);
        } catch (AlreadyExistsException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $entityMapping;
    }
}