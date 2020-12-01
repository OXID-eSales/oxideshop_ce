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

class ModuleUninstallCommand extends Command
{
    private const SUCCESS_MESSAGE = 'Module uninstalled successfully';
    private const ERROR_MESSAGE = 'Error uninstalling module: ';

    /** @var ModuleInstallerInterface */
    private $moduleInstaller;
    /** @var ModulePathResolverInterface */
    private $modulePathResolver;
    /** @var ContextInterface */
    private $context;

    public function __construct(
        ModuleInstallerInterface $moduleInstaller,
        ModulePathResolverInterface $modulePathResolver,
        ContextInterface $context
    ) {
        parent::__construct();
        $this->moduleInstaller = $moduleInstaller;
        $this->modulePathResolver = $modulePathResolver;
        $this->context = $context;
    }

    /** @inheritdoc */
    protected function configure(): void
    {
        $this->setDescription('Uninstall module assets and configuration')
            ->addArgument(
                'module-id',
                InputArgument::REQUIRED,
                'Module ID (see metadata.php)'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        try {
            $modulePath = $this->getModulePath($input->getArgument('module-id'));
            $this->moduleInstaller->uninstall($this->getPackage($modulePath));
            $style->success(self::SUCCESS_MESSAGE);
            return Command::SUCCESS;
        } catch (ModuleConfigurationNotFoundException $exception) {
            $style->error(self::ERROR_MESSAGE . $exception->getMessage());
        } catch (\Throwable $throwable) {
            $style->error(self::ERROR_MESSAGE . $throwable->getMessage());
            $style->text($throwable->getTraceAsString());
        }
        return Command::FAILURE;
    }

    private function getModulePath(string $moduleId): string
    {
        return $this->modulePathResolver
            ->getFullModulePathFromConfiguration($moduleId, $this->context->getDefaultShopId());
    }

    /**
     * @param string $modulePath
     * @return OxidEshopPackage
     */
    private function getPackage(string $modulePath): OxidEshopPackage
    {
        return new OxidEshopPackage($modulePath);
    }
}
