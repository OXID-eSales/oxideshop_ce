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

class ModuleInstallCommand extends Command
{
    private const SUCCESS_MESSAGE = 'Module installed successfully';
    private const ERROR_MESSAGE = 'Error installing module: ';

    public function __construct(
        private ModuleInstallerInterface $moduleInstaller
    ) {
        parent::__construct();
    }

    /** @inheritdoc */
    protected function configure(): void
    {
        $this->setDescription('Install module assets and configuration')
            ->addArgument(
                'module-path',
                InputArgument::REQUIRED,
                'Absolute or relative path to module files'
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
            $modulePath = $this->getModulePath($input->getArgument('module-path'));
            $this->moduleInstaller->install($this->getPackage($modulePath));
            $style->success(self::SUCCESS_MESSAGE);
            return Command::SUCCESS;
        } catch (\InvalidArgumentException $exception) {
            $style->error(self::ERROR_MESSAGE . $exception->getMessage());
        } catch (\Throwable $throwable) {
            $style->error(self::ERROR_MESSAGE . $throwable->getMessage());
            $style->text($throwable->getTraceAsString());
        }
        return Command::FAILURE;
    }

    /**
     * @param string $path
     * @return string
     */
    private function getModulePath(string $path): string
    {
        return Path::isRelative($path) ? Path::makeAbsolute($path, getcwd()) : $path;
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
