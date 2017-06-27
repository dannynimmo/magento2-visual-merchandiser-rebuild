<?php
/**
 * @author    Danny Nimmo <d@nny.nz>
 * @category  DannyNimmo\VisualMerchandiserRebuild
 * @copyright Copyright © 2017 Danny Nimmo
 */

namespace DannyNimmo\VisualMerchandiserRebuild\Console\Command;

use DannyNimmo\VisualMerchandiserRebuild\Model\Rebuilder;
use Magento\Framework\App\Area as AppArea;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RebuildCommand
    extends Command
{

    const NAME        = 'catalog:visual-merchandiser:rebuild';
    const DESCRIPTION = 'Rebuilds Visual Merchandiser categories';

    const MESSAGE_SUCCESS = 'Rebuilt %s Visual Merchandiser categories in %s';
    const MESSAGE_ERROR   = 'Error: %s';

    /**
     * Application state flags
     * @var AppState
     */
    protected $appState;

    /**
     * Visual Merchandiser rebuilder
     * @var Rebuilder
     */
    protected $rebuilder;

    /**
     * RebuildCommand constructor
     *
     * @param AppState $appState
     * @param Rebuilder $rebuilder
     */
    public function __construct (
        AppState $appState,
        Rebuilder $rebuilder
    ) {
        parent::__construct();
        $this->appState = $appState;
        $this->rebuilder = $rebuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure ()
    {
        $this
            ->setName(self::NAME)
            ->setDescription(self::DESCRIPTION)
        ;
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute (
        InputInterface $input,
        OutputInterface $output
    ) {
        try {
            try {
                $this->appState->setAreaCode(AppArea::AREA_ADMINHTML);
            } catch (LocalizedException $e) {
                // Area code was already set
            }

            $startTime  = microtime(true);
            $rebuiltIds = $this->rebuilder->rebuildAll();
            $resultTime = microtime(true) - $startTime;

            $count = count($rebuiltIds);
            $time  = gmdate('H:i:s', $resultTime);

            $output->writeln('<info>' . sprintf(self::MESSAGE_SUCCESS, $count, $time) . '</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . sprintf(self::MESSAGE_ERROR, $e->getMessage()) . '</error>');
        }
    }

}
