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
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplyModulesConfigurationCommand extends Command
{
    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ModuleActivationServiceInterface
     */
    private $moduleActivationService;

    /**
     * @var ModuleStateServiceInterface
     */
    private $moduleStateService;

    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ModuleActivationServiceInterface $moduleActivationService,
        ModuleStateServiceInterface $moduleStateService
    ) {
        parent::__construct();

        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->moduleActivationService = $moduleActivationService;
        $this->moduleStateService = $moduleStateService;
    }

    protected function configure()
    {
        $this->setDescription('Applies configuration for installed modules.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasOption('shop-id') && $input->getOption('shop-id')) {
            $this->applyModulesConfigurationForOneShop($output, (int) $input->getOption('shop-id'));
        } else {
            $this->applyModulesConfigurationForAllShops($output);
        }
    }

    private function applyModulesConfigurationForOneShop(OutputInterface $output, int $shopId): void
    {
        $shopConfiguration = $this->shopConfigurationDao->get($shopId);

        $this->applyModulesConfigurationForShop($output, $shopConfiguration, $shopId);
    }

    private function applyModulesConfigurationForAllShops(OutputInterface $output): void
    {
        $shopConfigurations = $this->shopConfigurationDao->getAll();

        foreach ($shopConfigurations as $shopId => $shopConfiguration) {
            $this->applyModulesConfigurationForShop($output, $shopConfiguration, $shopId);
        }
    }

    private function applyModulesConfigurationForShop(
        OutputInterface $output,
        ShopConfiguration $shopConfiguration,
        int $shopId
    ): void {
        $output->writeln('<info>Applying modules configuration for the shop with id ' . $shopId . ':</info>');

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $output->writeln(
                '<info>Applying configuration for module with id '
                . $moduleConfiguration->getId()
                . '</info>'
            );
            try {
                $this->deactivateNotConfiguredActivateModules($moduleConfiguration, $shopId);
                $this->reactivateConfiguredActiveModules($moduleConfiguration, $shopId);
                $this->activateConfiguredNotActiveModules($moduleConfiguration, $shopId);
            } catch (\Exception $exception) {
                $this->showErrorMessage($output, $exception);
            }
        }
    }

    private function deactivateNotConfiguredActivateModules(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        if ($moduleConfiguration->isConfigured() === false
            && $this->moduleStateService->isActive($moduleConfiguration->getId(), $shopId)
        ) {
            $this->moduleActivationService->deactivate($moduleConfiguration->getId(), $shopId);
        }
    }

    private function reactivateConfiguredActiveModules(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        if ($moduleConfiguration->isConfigured() === true
            && $this->moduleStateService->isActive($moduleConfiguration->getId(), $shopId) === true
        ) {
            $this->moduleActivationService->deactivate($moduleConfiguration->getId(), $shopId);
            $this->moduleActivationService->activate($moduleConfiguration->getId(), $shopId);
        }
    }

    private function activateConfiguredNotActiveModules(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        if ($moduleConfiguration->isConfigured() === true
            && $this->moduleStateService->isActive($moduleConfiguration->getId(), $shopId) === false
        ) {
            $this->moduleActivationService->activate($moduleConfiguration->getId(), $shopId);
        }
    }

    private function showErrorMessage(OutputInterface $output, \Exception $exception): void
    {
        $output->writeln(
            '<error>'
            . 'Module configuration wasn\'t applied. An exception occurred: '
            . \get_class($exception) . ' '
            . $exception->getMessage()
            . '</error>'
        );
    }
}
