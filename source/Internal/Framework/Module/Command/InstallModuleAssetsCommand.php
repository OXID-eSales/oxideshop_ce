<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleFilesInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webmozart\PathUtil\Path;

class InstallModuleAssetsCommand extends Command
{
    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @var ModuleFilesInstallerInterface
     */
    private $moduleFilesInstaller;

    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        BasicContextInterface $context,
        ModuleFilesInstallerInterface $moduleFilesInstaller
    ) {
        parent::__construct();

        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->context = $context;
        $this->moduleFilesInstaller = $moduleFilesInstaller;
    }


    protected function configure()
    {
        $this->setDescription(
            'Install assets for all modules (symlink or copy to the shop out directory depending on the platform).'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $shopConfiguration = $this->shopConfigurationDao->get($this->context->getDefaultShopId());

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $this->installModuleAsserts($moduleConfiguration);
        }

        $style = new SymfonyStyle($input, $output);
        $style->success('Module assets have been installed.');

        return Command::SUCCESS;
    }

    private function installModuleAsserts(ModuleConfiguration $moduleConfiguration): void
    {
        $this->moduleFilesInstaller->install(
            new OxidEshopPackage(
                Path::join($this->context->getShopRootPath(), $moduleConfiguration->getModuleSource())
            )
        );
    }
}
