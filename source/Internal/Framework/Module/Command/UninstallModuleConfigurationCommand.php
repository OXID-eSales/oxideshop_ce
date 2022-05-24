<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleConfigurationInstallerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated command will be superseded by oe:module:uninstall in next major
 */
class UninstallModuleConfigurationCommand extends Command
{
    const MESSAGE_REMOVE_WAS_SUCCESSFULL = 'Module configuration for module %s has been removed.';
    const MESSAGE_REMOVE_FAILED = 'An error occurred while removing module %s configuration.';

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
                'oe:module:uninstall-configuration'
            )
            ->setDescription(
                'Uninstall module configuration from project configuration file.'
            )
            ->addArgument('module-id', InputArgument::REQUIRED, 'Module ID, it can be found on moduleRootPath/metadata.php');
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
            $moduleId = $input->getArgument('module-id');
            $this->moduleConfigurationInstaller->uninstallById($moduleId);
            $output->writeln('<info>' . sprintf(self::MESSAGE_REMOVE_WAS_SUCCESSFULL, $moduleId) . '</info>');
        } catch (\Throwable $throwable) {
            $output->writeln('<error>' . sprintf(self::MESSAGE_REMOVE_FAILED, $moduleId) . '</error>');

            throw $throwable;
        }

        return 0;
    }
}
