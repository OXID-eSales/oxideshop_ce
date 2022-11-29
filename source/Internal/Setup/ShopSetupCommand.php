<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup;

use OxidEsales\EshopCommunity\Internal\Framework\Console\Command\NamedArgumentsTrait;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ShopStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\ConfigFileDaoInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Service\DirectoryValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessUpdaterInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;
use OxidEsales\EshopCommunity\Internal\Setup\Language\LanguageInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShopSetupCommand extends Command
{
    use NamedArgumentsTrait;

    private const DB_HOST = 'db-host';
    private const DB_PORT = 'db-port';
    private const DB_NAME = 'db-name';
    private const DB_USER = 'db-user';
    private const DB_PASSWORD = 'db-password';
    private const SHOP_URL = 'shop-url';
    private const SHOP_DIRECTORY = 'shop-directory';
    private const COMPILE_DIRECTORY = 'compile-directory';
    private const LANGUAGE = 'language';
    private const DEFAULT_LANG = 'en';

    private string $defaultTheme = 'twig';

    public function __construct(
        private DatabaseCheckerInterface $databaseChecker,
        private DatabaseInstallerInterface $databaseInstaller,
        private ConfigFileDaoInterface $configFileDao,
        private DirectoryValidatorInterface $directoriesValidator,
        private LanguageInstallerInterface $languageInstaller,
        private HtaccessUpdaterInterface $htaccessUpdateService,
        private ShopStateServiceInterface $shopStateService,
        private ShopAdapterInterface $shopAdapter
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addOption(self::DB_HOST, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::DB_PORT, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::DB_NAME, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::DB_USER, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::DB_PASSWORD, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::SHOP_URL, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::SHOP_DIRECTORY, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::COMPILE_DIRECTORY, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::LANGUAGE, null, InputOption::VALUE_OPTIONAL, '', self::DEFAULT_LANG);
        $this->setDescription('Performs initial shop setup');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkRequiredCommandOptions($this->getDefinition()->getOptions(), $input);

        $output->writeln('<info>Running pre-setup checks...</info>');
        $this->runPreSetupChecks($input);

        $output->writeln('<info>Updating config file...</info>');
        $this->updateConfigFile($input);

        $output->writeln('<info>Updating htaccess file...</info>');
        $this->htaccessUpdateService->updateRewriteBaseDirective($input->getOption(self::SHOP_URL));

        $output->writeln('<info>Installing database...</info>');
        $this->installDatabase($input);

        $output->writeln('<info>Installing language...</info>');
        $this->languageInstaller->install($this->getLanguage($input));

        if ($this->shopAdapter->themeExists($this->defaultTheme)) {
            $output->writeln("<info>Activating default ($this->defaultTheme) theme...</info>");
            $this->shopAdapter->activateTheme($this->defaultTheme);
        }

        $output->writeln('<info>Setup has been finished.</info>');

        return 0;
    }

    /**
     * @param InputInterface $input
     */
    private function runPreSetupChecks(InputInterface $input): void
    {
        $this->checkShopIsNotLaunched();
        $this->configFileDao->checkIsEditable();
        $this->checkCanCreateDatabase($input);
        $this->checkDirectories($input);
    }

    /** @throws ShopIsLaunchedException */
    private function checkShopIsNotLaunched(): void
    {
        if ($this->shopStateService->isLaunched()) {
            throw new ShopIsLaunchedException('Setup interrupted - shop is already launched.');
        }
    }

    /** @param InputInterface $input */
    private function checkCanCreateDatabase(InputInterface $input): void
    {
        $this->databaseChecker->canCreateDatabase(
            $input->getOption(self::DB_HOST),
            (int)$input->getOption(self::DB_PORT),
            $input->getOption(self::DB_USER),
            $input->getOption(self::DB_PASSWORD),
            $input->getOption(self::DB_NAME)
        );
    }

    /** @param InputInterface $input */
    private function checkDirectories(InputInterface $input): void
    {
        $this->directoriesValidator->checkPathIsAbsolute(
            $input->getOption(self::SHOP_DIRECTORY),
            $input->getOption(self::COMPILE_DIRECTORY)
        );

        $this->directoriesValidator->validateDirectory(
            $input->getOption(self::SHOP_DIRECTORY),
            $input->getOption(self::COMPILE_DIRECTORY)
        );

        $this->getLanguage($input);
    }

    /** @param InputInterface $input */
    private function updateConfigFile(InputInterface $input): void
    {
        $this->configFileDao->replacePlaceholder('sShopURL', $input->getOption(self::SHOP_URL));
        $this->configFileDao->replacePlaceholder('sShopDir', $input->getOption(self::SHOP_DIRECTORY));
        $this->configFileDao->replacePlaceholder('sCompileDir', $input->getOption(self::COMPILE_DIRECTORY));
    }

    /** @param InputInterface $input */
    private function installDatabase(InputInterface $input): void
    {
        $this->databaseInstaller->install(
            $input->getOption(self::DB_HOST),
            (int) $input->getOption(self::DB_PORT),
            $input->getOption(self::DB_USER),
            $input->getOption(self::DB_PASSWORD),
            $input->getOption(self::DB_NAME)
        );
    }

    /**
     * @param InputInterface $input
     * @return DefaultLanguage
     */
    private function getLanguage(InputInterface $input): DefaultLanguage
    {
        return new DefaultLanguage($input->getOption(self::LANGUAGE));
    }
}
