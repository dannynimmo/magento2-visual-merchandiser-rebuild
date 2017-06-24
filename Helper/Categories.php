<?php

namespace DannyNimmo\VisualMerchandiserRebuild\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\Store;
use Magento\VisualMerchandiser\Model\Rules;
use Magento\VisualMerchandiser\Model\RulesFactory;

/**
 * Class Categories
 * @package DannyNimmo\VisualMerchandiserRebuild\Helper
 */
class Categories extends AbstractHelper
{

    /**
     * Rules model
     * @var Rules
     */
    protected $rules;

    /**
     * Category Collection
     * @var CategoryCollection
     */
    protected $categoryCollection;

    /**
     * Categories constructor.
     * @param Context $context
     * @param RulesFactory $rulesFactory
     * @param CategoryCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        RulesFactory $rulesFactory,
        CategoryCollectionFactory $collectionFactory
    )
    {

        $this->rules = $rulesFactory->create();
        $this->categoryCollection = $collectionFactory->create();
        parent::__construct($context);
    }

    /**
     * Get collection of Visual Merchandiser categories
     *
     * @param $storeId
     * @return CategoryCollection
     */
    public function getSmartCategoryCollection($storeId = Store::DEFAULT_STORE_ID)
    {
        $this->categoryCollection->setStoreId($storeId);

        /** @var Category $category */
        foreach ($this->categoryCollection as $key => $category) {
            $rule = $this->rules->loadByCategory($category);
            if (!$rule->getId() || !$rule->getIsActive()) {
                $this->categoryCollection->removeItemByKey($key);
            }
        }

        return $this->categoryCollection;
    }

}