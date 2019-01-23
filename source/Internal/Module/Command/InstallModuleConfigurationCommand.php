<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Command;

use OxidEsales\EshopCommunity\Internal\Module\Install\ModuleConfigurationInstallerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
class InstallModuleConfigurationCommand extends Command
{
    const MESSAGE_INSTALLATION_WAS_SUCCESSFUL   = 'Module configuration has been installed.';
    const MESSAGE_INSTALLATION_FAILED           = 'An error occurred while installing module configuration.';

    /**
     * @var ModuleConfigurationInstallerInterface
     */
    private $moduleConfigurationInstaller;

    /**
     * @param ModuleConfigurationInstallerInterface $moduleConfigurationInstaller
     */
    public function __construct(ModuleConfigurationInstallerInterface $moduleConfigurationInstaller)
    {
        $this->moduleConfigurationInstaller = $moduleConfigurationInstaller;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('oe:module:install-configuration')
            ->setDescription('Install module configuration into project configuration file. Module configuration already present in the project configuration file will be overwriten.')
            ->addArgument('module-path', InputArgument::REQUIRED, 'Path to module source, e.g. vendor/myvendor/mymodule');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $modulePath = $input->getArgument('module-path');

        try {
            $this->moduleConfigurationInstaller->install($modulePath);
            $output->writeln('<info>' . self::MESSAGE_INSTALLATION_WAS_SUCCESSFUL . '</info>');
        } catch (\Throwable $throwable) {
            $output->writeln('<error>' . self::MESSAGE_INSTALLATION_FAILED . '</error>');

            throw $throwable;
        }
    }
}
