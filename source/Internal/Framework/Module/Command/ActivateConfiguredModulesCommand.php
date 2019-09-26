<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
class ActivateConfiguredModulesCommand extends Command
{
    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var ModuleActivationServiceInterface
     */
    private $moduleActivationService;

    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ContextInterface $context,
        ModuleActivationServiceInterface $moduleActivationService
    ) {
        parent::__construct();

        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->context = $context;
        $this->moduleActivationService = $moduleActivationService;
    }

    protected function configure()
    {
        $this->setDescription('Activates configured modules.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasOption('shop-id') && $input->getOption('shop-id')) {
            $this->activateModulesForOneShop($output, (int) $input->getOption('shop-id'));
        } else {
            $this->activateModuleForAllShops($output);
        }
    }

    private function activateModulesForOneShop(OutputInterface $output, int $shopId): void
    {
        $shopConfiguration = $this->shopConfigurationDao->get($shopId);

        $this->activateModulesForShop($output, $shopConfiguration, $shopId);
    }

    private function activateModuleForAllShops(OutputInterface $output): void
    {
        $shopConfigurations = $this->shopConfigurationDao->getAll();

        foreach ($shopConfigurations as $shopId => $shopConfiguration) {
            $this->activateModulesForShop($output, $shopConfiguration, $shopId);
        }
    }

    private function activateModulesForShop(
        OutputInterface $output,
        ShopConfiguration $shopConfiguration,
        int $shopId
    ): void {
        if (!$this->hasConfiguredModules($shopConfiguration)) {
            $output->writeln('<info>The shop with id ' . $shopId . ' doesn\'t have configured modules.</info>');
        } else {
            $output->writeln('<info>Activating modules for the shop with id ' . $shopId . ':</info>');

            foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
                if ($moduleConfiguration->isConfigured()) {
                    $this->activateModule($output, $moduleConfiguration, $shopId);
                }
            }
        }
    }

    private function hasConfiguredModules(ShopConfiguration $shopConfiguration): bool
    {
        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            if ($moduleConfiguration->isConfigured()) {
                return true;
            }
        }

        return false;
    }

    private function activateModule(
        OutputInterface $output,
        ModuleConfiguration $moduleConfiguration,
        int $shopId
    ): void {
        $output->writeln('<info>Activating module with id ' . $moduleConfiguration->getId() . '</info>');
        try {
            $this->moduleActivationService->activate($moduleConfiguration->getId(), $shopId);
        } catch (\Exception $exception) {
            $this->showErrorMessage($output, $exception);
        }
    }

    private function showErrorMessage(OutputInterface $output, \Exception $exception): void
    {
        $output->writeln(
            '<error>'
            . 'Module wasn\'t activated. An exception occurred: '
            . \get_class($exception) . ' '
            . $exception->getMessage()
            . '</error>'
        );
    }
}
