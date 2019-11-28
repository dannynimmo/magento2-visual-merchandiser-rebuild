<?php declare(strict_types=1);
/**
 * Copyright © Danny Nimmo. All rights reserved. See LICENSE file for license details.
 */

namespace DannyNimmo\VisualMerchandiserRebuild\Model;

use Exception;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Indexer\Category\Product as CategoryProductIndexer;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\VisualMerchandiser\Model\Rules;
use Magento\VisualMerchandiser\Model\RulesFactory;

/**
 * Rebuild Visual Merchandiser categories
 */
class Rebuilder
{
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var CategoryCollectionFactory
     */
    private $catCollectionFactory;

    /**
     * @var RulesFactory
     */
    private $rulesFactory;

    /**
     * @var CategoryProductIndexer
     */
    private $catProductIndexer;

    /**
     * Initialise dependencies
     *
     * @param StoreRepositoryInterface $storeRepository
     * @param CategoryCollectionFactory $catCollectionFactory
     * @param RulesFactory $rulesFactory
     * @param CategoryProductIndexer $catProductIndexer
     */
    public function __construct(
        StoreRepositoryInterface $storeRepository,
        CategoryCollectionFactory $catCollectionFactory,
        RulesFactory $rulesFactory,
        CategoryProductIndexer $catProductIndexer
    ) {
        $this->storeRepository = $storeRepository;
        $this->catCollectionFactory = $catCollectionFactory;
        $this->rulesFactory = $rulesFactory;
        $this->catProductIndexer = $catProductIndexer;
    }

    /**
     * Rebuild all Visual Merchandiser categories for all stores
     *
     * @return array
     * @throws Exception
     */
    public function rebuildAll(): array
    {
        $rebuiltIds = [];

        foreach ($this->storeRepository->getList() as $store) {
            $rebuiltIds[] = $this->rebuildStore($store->getId());
        }

        return array_merge([], ...$rebuiltIds);
    }

    /**
     * Rebuild Visual Merchandiser categories for a store
     *
     * @param int $storeId
     * @return int[] Rebuilt Category IDs
     * @throws Exception
     */
    public function rebuildStore($storeId = Store::DEFAULT_STORE_ID): array
    {
        $rebuiltIds = [];

        $categories = $this->catCollectionFactory->create();
        $this->filterCategories($categories, $storeId);

        /** @var Category $category */
        foreach ($categories as $category) {
            $categoryId = (int)$category->getId();
            $category
                ->setStoreId($storeId)
                ->load($categoryId)
                ->save();
            $rebuiltIds[] = $categoryId;
        }

        $this->catProductIndexer->executeList($rebuiltIds);

        return $rebuiltIds;
    }

    /**
     * Remove categories without Visual Merchandiser rules from the
     * passed in collection
     *
     * @param CategoryCollection $categories
     * @param int $storeId
     * @return void
     */
    private function filterCategories(
        CategoryCollection $categories,
        $storeId = Store::DEFAULT_STORE_ID
    ) {
        $categories->setStoreId($storeId);
        /** @var Category $category */
        foreach ($categories as $key => $category) {
            /** @var Rules $rules */
            $rules = $this->rulesFactory->create();
            $rules->loadByCategory($category);
            if (!$rules->getId() || !$rules->getIsActive()) {
                $categories->removeItemByKey($key);
            }
        }
    }
}
