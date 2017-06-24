<?php

namespace Dakzilla\VisualMerchandiserRebuild\Console\Command;

use Dakzilla\VisualMerchandiserRebuild\Helper\Categories as CategoryHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{

    const NAME = 'catalog:visual-merchandiser:show';
    const DESCRIPTION = 'Shows Visual Merchandiser categories information';

    /**
     * Product category helper
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * Magento App State
     * @var State
     */
    protected $state;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * RebuildCommand constructor
     *
     * @param CategoryHelper $categoryHelper
     * @param State $state
     * @param CategoryRepository $categoryRepository
     * @throws \LogicException
     */
    public function __construct(
        CategoryHelper $categoryHelper,
        State $state,
        CategoryRepository $categoryRepository
    )
    {
        $this->categoryHelper = $categoryHelper;
        $this->state = $state;
        $this->categoryRepository = $categoryRepository;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION);
        parent::configure();
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        try {
            $this->state->getAreaCode();
        } catch (LocalizedException $e) {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        }

        try {
            $smartCategoryCollection = $this->categoryHelper->getSmartCategoryCollection();
            $table = new Table($output);
            $table->setHeaders(array('Category ID', 'Category Name', 'Product count', 'Root category name'));

            foreach ($smartCategoryCollection as $smartCategory) {
                /** @var $category Category */
                $category = $this->categoryRepository->get($smartCategory->getId());
                $rootCategory = $this->_getRootCategory($category);
                $table->addRow([
                    $category->getId(),
                    $category->getName(),
                    $category->getProductCount(),
                    $rootCategory ? $rootCategory->getName() : null
                ]);
            }

            $table->render();
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     * @param Category $category
     * @return bool|Category
     */
    protected function _getRootCategory(Category $category)
    {
        $parentCategories = $category->getParentCategories();

        if (!count($parentCategories)) {
            return false;
        }

        return reset($parentCategories);
    }

}
