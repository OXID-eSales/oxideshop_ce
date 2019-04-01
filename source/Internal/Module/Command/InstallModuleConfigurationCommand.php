<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Command;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Install\Service\ModuleConfigurationInstallerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class InstallModuleConfigurationCommand extends Command
{
    const MESSAGE_INSTALLATION_WAS_SUCCESSFUL   = 'Module configuration has been installed.';
    const MESSAGE_INSTALLATION_FAILED           = 'An error occurred while installing module configuration.';
    const MESSAGE_TARGET_PATH_IS_REQUIRED       = 'The module source path is not in the shop modules directory. Please provide additional parameter with module target path.';

    /**
     * @var ModuleConfigurationInstallerInterface
     */
    private $moduleConfigurationInstaller;

    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @param ModuleConfigurationInstallerInterface $moduleConfigurationInstaller
     * @param BasicContextInterface                 $context
     */
    public function __construct(
        ModuleConfigurationInstallerInterface $moduleConfigurationInstaller,
        BasicContextInterface $context
    ) {
        $this->moduleConfigurationInstaller = $moduleConfigurationInstaller;
        $this->context = $context;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName(
                'oe:module:install-configuration'
            )
            ->setDescription(
                'Install module configuration into project configuration file. Module configuration already present in the project configuration file will be overwriten.'
            )
            ->addArgument(
                'module-source-path',
                InputArgument::REQUIRED,
                'Path to module source, e.g. vendor/myvendor/mymodule'
            )
            ->addArgument(
                'module-target-path',
                InputArgument::OPTIONAL,
                'Path to module target, e.g. myModules/module or source/modules/myModules/module'
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->moduleConfigurationInstaller->install(
                $this->getModuleSourcePath($input),
                $this->getModuleTargetPath($input)
            );

            $output->writeln('<info>' . self::MESSAGE_INSTALLATION_WAS_SUCCESSFUL . '</info>');
        } catch (ModuleTargetPathIsMissingException $exception) {
            $output->writeln('<error>' . self::MESSAGE_TARGET_PATH_IS_REQUIRED . '</error>');
        } catch (\Throwable $throwable) {
            $output->writeln('<error>' . self::MESSAGE_INSTALLATION_FAILED . '</error>');

            throw $throwable;
        }
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    private function getModuleSourcePath(InputInterface $input): string
    {
        return $input->getArgument('module-source-path');
    }

    /**
     * @param InputInterface $input
     * @return string
     * @throws ModuleTargetPathIsMissingException
     */
    private function getModuleTargetPath(InputInterface $input): string
    {
        $moduleTargetPath = $input->getArgument('module-target-path');
        if ($moduleTargetPath !== null) {
            return $moduleTargetPath;
        }

        $moduleSourcePath = $this->getModuleSourcePath($input);
        if ($this->isItModulesTargetDirectory($moduleSourcePath)) {
            return $moduleSourcePath;
        }

        throw new ModuleTargetPathIsMissingException();
    }

    /**
     * @param string $path
     * @return bool
     */
    private function isItModulesTargetDirectory(string $path): bool
    {
        if (Path::isRelative($path)) {
            $path = Path::join($this->context->getShopRootPath(), $path);
        }

        return Path::isBasePath($this->context->getModulesPath(), $path);
    }
}
