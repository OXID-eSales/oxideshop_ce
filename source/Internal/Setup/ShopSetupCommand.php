<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Service\AdminUserServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ShopStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\ConfigFileDaoInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Service\DirectoryValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessUpdateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;
use OxidEsales\EshopCommunity\Internal\Setup\Language\LanguageInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Console\Command\NamedArgumentsTrait;
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
    private const ADMIN_EMAIL = 'admin-email';
    private const ADMIN_PASSWORD = 'admin-password';
    private const LANGUAGE = 'language';
    private const DEFAULT_LANG = 'en';

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
     * @var HtaccessUpdateServiceInterface
     */
    private $htaccessUpdateService;

    /**
     * @var AdminUserServiceInterface
     */
    private $adminService;

    /**
     * @var ShopStateServiceInterface
     */
    private $shopStateService;

    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    public function __construct(
        DatabaseInstallerInterface $databaseInstaller,
        ConfigFileDaoInterface $configFileDao,
        DirectoryValidatorInterface $directoriesValidator,
        LanguageInstallerInterface $languageInstaller,
        HtaccessUpdateServiceInterface $htaccessUpdateService,
        AdminUserServiceInterface $adminService,
        ShopStateServiceInterface $shopStateService,
        BasicContextInterface $basicContext
    ) {
        parent::__construct();

        $this->databaseInstaller = $databaseInstaller;
        $this->configFileDao = $configFileDao;
        $this->directoriesValidator = $directoriesValidator;
        $this->languageInstaller = $languageInstaller;
        $this->htaccessUpdateService = $htaccessUpdateService;
        $this->adminService = $adminService;
        $this->shopStateService = $shopStateService;
        $this->basicContext = $basicContext;
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
            ->addOption(self::ADMIN_EMAIL, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::ADMIN_PASSWORD, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::LANGUAGE, null, InputOption::VALUE_OPTIONAL, '', self::DEFAULT_LANG);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws DbExistsAndNotEmptyException
     * @throws ShopIsLaunchedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->validateRequiredOptions($this->getDefinition()->getOptions(), $input);

        $this->checkCanCreateDb($input);
        $this->checkCanUpdateConfigFile();

        $output->writeln('<info>Validating input...</info>');
        $this->validateInput($input);

        $output->writeln('<info>Updating config file...</info>');
        $this->updateConfigFile($input);

        $output->writeln('<info>Updating htaccess file...</info>');
        $this->htaccessUpdateService->updateRewriteBaseDirective($input->getOption(self::SHOP_URL));

        $output->writeln('<info>Installing database data...</info>');
        $this->installDatabase($input);
        $this->languageInstaller->install($this->getLanguage($input));

        $output->writeln('<info>Creating administrator account...</info>');
        $this->adminService->createAdmin(
            $input->getOption(self::ADMIN_EMAIL),
            $input->getOption(self::ADMIN_PASSWORD),
            Admin::MALL_ADMIN,
            $this->basicContext->getDefaultShopId()
        );

        $output->writeln('<info>Setup has been finished.</info>');

        return 0;
    }

    protected function installDatabase(InputInterface $input): void
    {
        $this->databaseInstaller->install(
            $input->getOption(self::DB_HOST),
            (int) $input->getOption(self::DB_PORT),
            $input->getOption(self::DB_USER),
            $input->getOption(self::DB_PASSWORD),
            $input->getOption(self::DB_NAME)
        );
    }

    private function updateConfigFile(InputInterface $input): void
    {
        $this->configFileDao->replacePlaceholder('sShopURL', $input->getOption(self::SHOP_URL));
        $this->configFileDao->replacePlaceholder('sShopDir', $input->getOption(self::SHOP_DIRECTORY));
        $this->configFileDao->replacePlaceholder('sCompileDir', $input->getOption(self::COMPILE_DIRECTORY));
    }

    private function getLanguage(InputInterface $input): DefaultLanguage
    {
        return new DefaultLanguage($input->getOption(self::LANGUAGE));
    }

    protected function validateInput(InputInterface $input): void
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

    /**
     * @param InputInterface $input
     * @throws DbExistsAndNotEmptyException
     */
    private function checkCanCreateDb(InputInterface $input): void
    {
        $dbExists = $this->shopStateService->checkIfDbExistsAndNotEmpty(
            $input->getOption(self::DB_HOST),
            (int)$input->getOption(self::DB_PORT),
            $input->getOption(self::DB_USER),
            $input->getOption(self::DB_PASSWORD),
            $input->getOption(self::DB_NAME)
        );
        if ($dbExists) {
            throw new DbExistsAndNotEmptyException(
                sprintf('Database `%s` already exists and is not empty', $input->getOption(self::DB_NAME))
            );
        }
    }

    /** @throws ShopIsLaunchedException */
    private function checkCanUpdateConfigFile(): void
    {
        if ($this->shopStateService->isLaunched()) {
            throw new ShopIsLaunchedException('Configuration file can\'t be updated - shop was launched before');
        }
    }
}
