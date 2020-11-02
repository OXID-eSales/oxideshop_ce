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
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
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

    /**
     * @var DatabaseCheckerInterface
     */
    private $databaseChecker;

    /**
     * @var DatabaseInstallerInterface
     */
    private $databaseInstaller;

    /**
     * @var ConfigFileDaoInterface
     */
    private $configFileDao;

    /**
     * @var DirectoryValidatorInterface
     */
    private $directoriesValidator;

    /**
     * @var LanguageInstallerInterface
     */
    private $languageInstaller;

    /**
     * @var HtaccessUpdaterInterface
     */
    private $htaccessUpdateService;

    /**
     * @var ShopStateServiceInterface
     */
    private $shopStateService;

    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    public function __construct(
        DatabaseCheckerInterface $databaseChecker,
        DatabaseInstallerInterface $databaseInstaller,
        ConfigFileDaoInterface $configFileDao,
        DirectoryValidatorInterface $directoriesValidator,
        LanguageInstallerInterface $languageInstaller,
        HtaccessUpdaterInterface $htaccessUpdateService,
        ShopStateServiceInterface $shopStateService,
        BasicContextInterface $basicContext
    ) {
        parent::__construct();

        $this->databaseChecker = $databaseChecker;
        $this->databaseInstaller = $databaseInstaller;
        $this->configFileDao = $configFileDao;
        $this->directoriesValidator = $directoriesValidator;
        $this->languageInstaller = $languageInstaller;
        $this->htaccessUpdateService = $htaccessUpdateService;
        $this->shopStateService = $shopStateService;
        $this->basicContext = $basicContext;
    }

    protected function configure(): void
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

        $output->writeln('<info>Setup has been finished.</info>');

        return 0;
    }

    private function runPreSetupChecks(InputInterface $input): void
    {
        $this->checkShopIsNotLaunched();
        $this->configFileDao->checkIsEditable();
        $this->checkCanCreateDatabase($input);
        $this->checkDirectories($input);
    }

    /**
     * @throws ShopIsLaunchedException
     */
    private function checkShopIsNotLaunched(): void
    {
        if ($this->shopStateService->isLaunched()) {
            throw new ShopIsLaunchedException('Setup interrupted - shop is already launched.');
        }
    }

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

    private function updateConfigFile(InputInterface $input): void
    {
        $this->configFileDao->replacePlaceholder('sShopURL', $input->getOption(self::SHOP_URL));
        $this->configFileDao->replacePlaceholder('sShopDir', $input->getOption(self::SHOP_DIRECTORY));
        $this->configFileDao->replacePlaceholder('sCompileDir', $input->getOption(self::COMPILE_DIRECTORY));
    }

    private function installDatabase(InputInterface $input): void
    {
        $this->databaseInstaller->install(
            $input->getOption(self::DB_HOST),
            (int)$input->getOption(self::DB_PORT),
            $input->getOption(self::DB_USER),
            $input->getOption(self::DB_PASSWORD),
            $input->getOption(self::DB_NAME)
        );
    }

    private function getLanguage(InputInterface $input): DefaultLanguage
    {
        return new DefaultLanguage($input->getOption(self::LANGUAGE));
    }
}
