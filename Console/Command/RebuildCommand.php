<?php
/**
 * @author    Danny Nimmo <d@nny.nz>
 * @category  DannyNimmo\VisualMerchandiserRebuild
 * @copyright Copyright © 2017 Danny Nimmo
 */

namespace DannyNimmo\VisualMerchandiserRebuild\Console\Command;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Indexer\Category\Product as CategoryProductIndexer;
use Magento\Catalog\Model\Indexer\Category\ProductFactory as CategoryProductIndexerFactory;
use DannyNimmo\VisualMerchandiserRebuild\Helper\Categories as CategoryHelper;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Store\Model\Store;

class RebuildCommand extends Command
{

    const NAME = 'catalog:visual-merchandiser:rebuild';
    const DESCRIPTION = 'Rebuilds Visual Merchandiser categories';

    const MESSAGE_SUCCESS = 'Rebuilt %s Visual Merchandiser categories in %s';
    const MESSAGE_ERROR = 'Error: %s';

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
     * Category Product Indexer model
     * @var CategoryProductIndexer
     */
    protected $categoryProductIndexer;

    /**
     * RebuildCommand constructor
     *
     * @param State $state
     * @param CategoryProductIndexerFactory $categoryProductIndexerFactory
     * @param CategoryHelper $categoryHelper
     * @throws \LogicException
     */
    public function __construct(
        State $state,
        CategoryProductIndexerFactory $categoryProductIndexerFactory,
        CategoryHelper $categoryHelper
    )
    {
        $this->state = $state;
        $this->categoryProductIndexer = $categoryProductIndexerFactory->create();
        $this->categoryHelper = $categoryHelper;
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
            $startTime = microtime(true);
            $rebuiltIds = $this->rebuildAll();
            $resultTime = microtime(true) - $startTime;
            $count = count($rebuiltIds);
            $time = gmdate('H:i:s', $resultTime);

            $output->writeln('<info>' . sprintf(self::MESSAGE_SUCCESS, $count, $time) . '</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . sprintf(self::MESSAGE_ERROR, $e->getMessage()) . '</error>');
        }
    }

    /**
     * Rebuild all Visual Merchandiser categories
     *
     * @param int $storeId
     * @return \int[] Rebuilt Category IDs
     * @throws \Exception
     */
    protected function rebuildAll($storeId = Store::DEFAULT_STORE_ID)
    {
        $rebuiltIds = [];
        $smartCategoryCollection = $this->categoryHelper->getSmartCategoryCollection($storeId);

        /** @var Category $category */
        foreach ($smartCategoryCollection as $smartCategory) {
            $smartCategoryId = (int)$smartCategory->getId();
            $smartCategory
                ->setStoreId($storeId)
                ->load($smartCategoryId)
                ->save();
            $rebuiltIds[] = $smartCategoryId;
        }

        $this->categoryProductIndexer->executeList($rebuiltIds);

        return $rebuiltIds;
    }

}
