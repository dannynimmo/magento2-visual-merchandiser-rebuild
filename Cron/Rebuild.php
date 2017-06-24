<?php

namespace Dakzilla\VisualMerchandiserRebuild\Cron;

use Dakzilla\VisualMerchandiserRebuild\Helper\Categories as CategoryHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class Rebuild
{

    /**
     * Product category helper
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * Rebuild constructor.
     * @param CategoryHelper $categoryHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        CategoryHelper $categoryHelper,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    )
    {
        $this->categoryHelper = $categoryHelper;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    public function execute()
    {
        $enabled = (bool)$this->scopeConfig->getValue('visualmerchandiser/options/cronjob_enabled');

        if (!$enabled) {
            return false;
        }

        $this->logger->debug('Visual Merchandiser Rebuilder - Started rebuilding categories');
        $rebuiltIds = $this->categoryHelper->rebuildAll();
        $this->logger->debug('Visual Merchandiser Rebuilder - Finished rebuilding ' . count($rebuiltIds) . ' categories');
    }

}