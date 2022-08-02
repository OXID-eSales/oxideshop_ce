<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Console\Executor;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplyModulesConfigurationCommand extends Command
{
    public function __construct(
        private ShopConfigurationDaoInterface $shopConfigurationDao,
        private ModuleActivationServiceInterface $moduleActivationService
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Applies configuration for installed modules.')
        ->addArgument(
            Executor::SHOP_ID_PARAMETER_OPTION_NAME,
            InputArgument::OPTIONAL,
            'Id of shop this module configuration should be applied to'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shopId = $input->getArgument(Executor::SHOP_ID_PARAMETER_OPTION_NAME);
        if ($shopId) {
            $this->applyModulesConfigurationForOneShop($output, (int) $shopId);
        } else {
            $this->applyModulesConfigurationForAllShops($output);
        }

        return 0;
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
                $this->reactivateConfiguredActiveModules($moduleConfiguration, $shopId);
            } catch (\Exception $exception) {
                $this->showErrorMessage($output, $exception);
            }
        }
    }

    private function reactivateConfiguredActiveModules(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        if ($moduleConfiguration->isActivated()) {
            $this->moduleActivationService->deactivate($moduleConfiguration->getId(), $shopId);
            $this->moduleActivationService->activate($moduleConfiguration->getId(), $shopId);
        }
    }

    private function showErrorMessage(OutputInterface $output, \Exception $exception): void
    {
        $output->writeln(
            '<error>'
            . 'Module configuration wasn\'t applied. An exception occurred: '
            . $exception::class . ' '
            . $exception->getMessage()
            . '</error>'
        );
    }
}
