<?php declare(strict_types=1);
/**
 * Copyright © Danny Nimmo. All rights reserved. See LICENSE file for license details.
 */

namespace DannyNimmo\VisualMerchandiserRebuild\Console\Command;

use DannyNimmo\VisualMerchandiserRebuild\Model\Rebuilder;
use Exception;
use Magento\Framework\App\Area as AppArea;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console CLI command to rebuild the visual merchandiser categories
 */
class RebuildCommand extends Command
{
    const NAME        = 'catalog:visual-merchandiser:rebuild';
    const DESCRIPTION = 'Rebuilds Visual Merchandiser categories';

    const MESSAGE_SUCCESS = 'Rebuilt %s Visual Merchandiser categories';
    const MESSAGE_ERROR   = 'Error: %s';

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var Rebuilder
     */
    protected $rebuilder;

    /**
     * Initialise dependencies
     *
     * @param AppState $appState
     * @param Rebuilder $rebuilder
     */
    public function __construct(
        AppState $appState,
        Rebuilder $rebuilder
    ) {
        parent::__construct();
        $this->appState = $appState;
        $this->rebuilder = $rebuilder;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::NAME);
        $this->setDescription(self::DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        try {
            try {
                $this->appState->setAreaCode(AppArea::AREA_ADMINHTML);
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
            } catch (LocalizedException $e) {
                // Area code was already set
            }

            $rebuiltIds = $this->rebuilder->rebuildAll();

            $output->writeln('<info>' . sprintf(self::MESSAGE_SUCCESS, count($rebuiltIds)) . '</info>');
        } catch (Exception $e) {
            $output->writeln('<error>' . sprintf(self::MESSAGE_ERROR, $e->getMessage()) . '</error>');
        }
    }
}
