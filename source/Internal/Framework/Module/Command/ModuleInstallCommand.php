<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Path;

final class ModuleInstallCommand extends Command
{
    public function __construct(
        private ModuleInstallerInterface $moduleInstaller
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Install module assets and configuration')
            ->addArgument(
                'module-path',
                InputArgument::REQUIRED,
                'Absolute or relative path to module files'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $modulePath = $this->getModulePath($input->getArgument('module-path'));
        $this->moduleInstaller->install($this->getPackage($modulePath));
        $style->success('Module installed successfully');
        return Command::SUCCESS;
    }

    private function getModulePath(string $path): string
    {
        return Path::isRelative($path) ? Path::makeAbsolute($path, getcwd()) : $path;
    }

    private function getPackage(string $modulePath): OxidEshopPackage
    {
        return new OxidEshopPackage($modulePath);
    }
}