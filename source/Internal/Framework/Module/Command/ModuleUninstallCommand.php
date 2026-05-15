<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ModuleUninstallCommand extends Command
{
    public function __construct(
        private ModuleInstallerInterface $moduleInstaller,
        private ModulePathResolverInterface $modulePathResolver,
        private ContextInterface $context
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Uninstall module assets and configuration')
            ->addArgument(
                'module-id',
                InputArgument::REQUIRED,
                'Module ID (see metadata.php)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        try {
            $modulePath = $this->getModulePath($input->getArgument('module-id'));
            $this->moduleInstaller->uninstall($this->getPackage($modulePath));
            $style->success('Module uninstalled successfully');
            return Command::SUCCESS;
        } catch (ModuleConfigurationNotFoundException $exception) {
            $style->error('Error uninstalling module: ' . $exception->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * @throws ModuleConfigurationNotFoundException
     */
    private function getModulePath(string $moduleId): string
    {
        return $this->modulePathResolver
            ->getFullModulePathFromConfiguration($moduleId, $this->context->getDefaultShopId());
    }

    private function getPackage(string $modulePath): OxidEshopPackage
    {
        return new OxidEshopPackage($modulePath);
    }
}