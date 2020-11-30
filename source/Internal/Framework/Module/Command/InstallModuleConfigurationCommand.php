<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Exception\PathNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\PathUtil\Path;

class InstallModuleConfigurationCommand extends Command
{
    public const MESSAGE_INSTALLATION_WAS_SUCCESSFUL = 'Module configuration has been installed.';
    public const MESSAGE_INSTALLATION_FAILED = 'An error occurred while installing module configuration.';

    /**
     * @var ModuleConfigurationInstallerInterface
     */
    private $moduleConfigurationInstaller;

    /**
     * @param ModuleConfigurationInstallerInterface $moduleConfigurationInstaller
     */
    public function __construct(
        ModuleConfigurationInstallerInterface $moduleConfigurationInstaller
    ) {
        $this->moduleConfigurationInstaller = $moduleConfigurationInstaller;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this
            ->setName(
                'oe:module:install-configuration'
            )
            ->setDescription(
                'Install module configuration into project configuration file.'
                . 'Module configuration already present in the project configuration file will be overwritten.'
            )
            ->addArgument(
                'module-source-path',
                InputArgument::REQUIRED,
                'Path to module source, e.g. vendor/my_vendor/my_module;'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $moduleSourcePath = $this->getModuleSourcePath($input);
            $this->validatePath($moduleSourcePath);

            $this->moduleConfigurationInstaller->install($moduleSourcePath);
            $output->writeln('<info>' . self::MESSAGE_INSTALLATION_WAS_SUCCESSFUL . '</info>');
        } catch (PathNotFoundException $exception) {
            $output->writeln('<error>' . self::MESSAGE_INSTALLATION_FAILED . ': ' . $exception->getMessage() . '</error>');
        } catch (\Throwable $throwable) {
            $output->writeln('<error>' . self::MESSAGE_INSTALLATION_FAILED . '</error>');

            throw $throwable;
        }

        return 0;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    private function getModuleSourcePath(InputInterface $input): string
    {
        return $this->getAbsolutePath($input->getArgument('module-source-path'));
    }


    /**
     * @param string $path
     */
    private function validatePath(string $path)
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw PathNotFoundException::byPath($path);
        }
    }

    /**
     * @param string $path
     * @return string
     */
    private function getAbsolutePath(string $path): string
    {
        return Path::isRelative($path)
            ? Path::makeAbsolute($path, getcwd())
            : $path;
    }
}
