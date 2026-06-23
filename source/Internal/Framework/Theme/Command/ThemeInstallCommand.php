<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\DataObject\OxidThemePackage;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeInstallerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Path;

final class ThemeInstallCommand extends Command
{
    public function __construct(
        private readonly ThemeInstallerInterface $themeInstaller
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Install theme assets and configuration')
            ->addArgument(
                'theme-path',
                InputArgument::REQUIRED,
                'Absolute or relative path to theme files'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $themePath = $this->getThemePath($input->getArgument('theme-path'));
        $this->themeInstaller->install(new OxidThemePackage($themePath));
        $style->success('Theme installed successfully');

        return Command::SUCCESS;
    }

    private function getThemePath(string $path): string
    {
        return Path::isRelative($path) ? Path::makeAbsolute($path, getcwd()) : $path;
    }
}
