<?php
/**
 * @author    Danny Nimmo <d@nny.nz>
 * @category  DannyNimmo\VisualMerchandiserRebuild
 * @copyright Copyright © 2017 Danny Nimmo
 */

namespace DannyNimmo\VisualMerchandiserRebuild\Model;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Indexer\Category\Product as CategoryProductIndexer;
use Magento\Catalog\Model\Indexer\Category\ProductFactory as CategoryProductIndexerFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\VisualMerchandiser\Model\Category\Builder;
use Magento\VisualMerchandiser\Model\Category\BuilderFactory;
use Magento\VisualMerchandiser\Model\Rules;
use Magento\VisualMerchandiser\Model\RulesFactory;

class Rebuilder
{

    /**
     * Category Collection
     * @var CategoryCollection
     */
    protected $categoryCollection;

    /**
     * Rules model
     * @var Rules
     */
    protected $rules;

    /**
     * Visual Merchandiser Builder model
     * @var Builder
     */
    protected $builder;

    /**
     * Category Product Indexer model
     * @var CategoryProductIndexer
     */
    protected $categoryProductIndexer;

    /**
     * Rebuilder constructor
     *
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param RulesFactory $rulesFactory
     * @param BuilderFactory $builderFactory
     * @param CategoryProductIndexerFactory $categoryProductIndexerFactory
     */
    public function __construct (
        CategoryCollectionFactory $categoryCollectionFactory,
        RulesFactory $rulesFactory,
        BuilderFactory $builderFactory,
        CategoryProductIndexerFactory $categoryProductIndexerFactory
    ) {
        $this->categoryCollection = $categoryCollectionFactory->create();
        $this->rules = $rulesFactory->create();
        $this->builder = $builderFactory->create();
        $this->categoryProductIndexer = $categoryProductIndexerFactory->create();
    }

    /**
     * Remove categories without Visual Merchandiser rules
     *
     * @return void
     */
    public function filterCategories ()
    {
        /** @var Category $category */
        foreach ($this->categoryCollection as $key => $category) {
            $rule = $this->rules->loadByCategory($category);
            if (!$rule->getId() || !$rule->getIsActive()) {
                $this->categoryCollection->removeItemByKey($key);
            }
        }
    }

    /**
     * Rebuild all Visual Merchandiser categories
     *
     * @return int[] Rebuilt Category IDs
     */
    public function rebuildAll ()
    {
        $rebuiltIds = [];

        $this->filterCategories();

        /** @var Category $category */
        foreach ($this->categoryCollection as $category) {
            $category->save();
            $rebuiltIds[] = (int) $category->getId();
        }

        $this->categoryProductIndexer->executeList($rebuiltIds);

        return $rebuiltIds;
    }

}
