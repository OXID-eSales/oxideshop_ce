<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup;

use PHPUnit\Framework\Attributes\DataProvider;
use InvalidArgumentException;
use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ShopStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\ConfigFileDaoInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\FileNotEditableException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsAndNotEmptyException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Service\DirectoryValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessUpdaterInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;
use OxidEsales\EshopCommunity\Internal\Setup\Language\LanguageInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ShopIsLaunchedException;
use OxidEsales\EshopCommunity\Internal\Setup\ShopSetupCommand;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class ShopSetupCommandTest extends IntegrationTestCase
{
    use ProphecyTrait;
    use ContainerTrait;

    private const HOST = 'some-host';
    private const PORT = 123;
    private const DB = 'some-db';
    private const DB_USER = 'some-db-user';
    private const DB_PASS = 'some-db-pass';
    private const URL = 'some-url';
    private const DIR = 'some-dir';
    private const TMP_DIR = 'some-tmp-dir';
    private const LANG = 'de';
    private const DEFAULT_LANG = 'en';
    private const DEFAULT_SHOP_ID = 12345;
    private const DEFAULT_THEME = 'twig';

    private CommandTester $commandTester;
    private DatabaseCheckerInterface|ObjectProphecy $databaseChecker;
    private DatabaseInstallerInterface|ObjectProphecy $databaseInstall;
    private ConfigFileDaoInterface|ObjectProphecy $configFileDao;
    private ObjectProphecy|DirectoryValidatorInterface $directoryValidator;
    private ObjectProphecy|LanguageInstallerInterface $languageInstaller;
    private HtaccessUpdaterInterface|ObjectProphecy $htaccessUpdateService;
    private ShopStateServiceInterface|ObjectProphecy $shopStateService;
    private ObjectProphecy|BasicContextInterface $basicContext;
    private ObjectProphecy|ShopAdapterInterface $shopAdapter;

    public function setUp(): void
    {
        parent::setUp();

        $this->commandTester = new CommandTester($this->createCommand());
    }

    #[DataProvider('missingOptionsDataProvider')]
    public function testExecuteWithMissingArgs(string $command): void
    {
        $options = [
            '--db-host'           => self::HOST,
            '--db-port'           => self::PORT,
            '--db-name'           => self::DB,
            '--db-user'           => self::DB_USER,
            '--db-password'       => self::DB_PASS,
            '--shop-url'          => self::URL,
            '--shop-directory'    => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language'          => self::LANG,
        ];

        $this->expectException(InvalidArgumentException::class);

        unset($options[$command]);
        $this->commandTester->execute($options);
    }

    public static function missingOptionsDataProvider(): array
    {
        return [
            'Missing db-host'           => ['--db-host'],
            'Missing db-port'           => ['--db-port'],
            'Missing db-name'           => ['--db-name'],
            'Missing db-user'           => ['--db-user'],
            'Missing db-password'       => ['--db-password'],
            'Missing shop-url'          => ['--shop-url'],
            'Missing shop-directory'    => ['--shop-directory'],
            'Missing compile-directory' => ['--compile-directory'],
        ];
    }

    public function testExecuteWithShopAlreadyLaunched(): void
    {
        $this->shopStateService->isLaunched()
            ->willReturn(true);

        $this->expectException(ShopIsLaunchedException::class);

        $this->commandTester->execute([
            '--db-host'           => self::HOST,
            '--db-port'           => self::PORT,
            '--db-name'           => self::DB . uniqid('_', false),
            '--db-user'           => self::DB_USER,
            '--db-password'       => self::DB_PASS,
            '--shop-url'          => self::URL,
            '--shop-directory'    => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language'          => self::LANG,
        ]);
    }

    public function testExecuteWithConfigNotEditable(): void
    {
        $this->shopStateService->isLaunched()
            ->willReturn(false);
        $this->configFileDao->checkIsEditable()
            ->willThrow(FileNotEditableException::class);

        $this->expectException(FileNotEditableException::class);

        $this->commandTester->execute([
            '--db-host'           => self::HOST,
            '--db-port'           => self::PORT,
            '--db-name'           => self::DB . uniqid('_', false),
            '--db-user'           => self::DB_USER,
            '--db-password'       => self::DB_PASS,
            '--shop-url'          => self::URL,
            '--shop-directory'    => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language'          => self::LANG,
        ]);
    }

    public function testExecuteWithExistingDb(): void
    {
        $this->shopStateService->isLaunched()
            ->willReturn(false);

        $this->databaseChecker->canCreateDatabase(
            self::HOST,
            self::PORT,
            self::DB_USER,
            self::DB_PASS,
            self::DB
        )
            ->willThrow(DatabaseExistsAndNotEmptyException::class);

        $this->expectException(DatabaseExistsAndNotEmptyException::class);

        $this->commandTester->execute([
            '--db-host'           => self::HOST,
            '--db-port'           => self::PORT,
            '--db-name'           => self::DB,
            '--db-user'           => self::DB_USER,
            '--db-password'       => self::DB_PASS,
            '--shop-url'          => self::URL,
            '--shop-directory'    => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language'          => self::LANG,
        ]);
    }

    public function testExecuteWithCompleteArgs(): void
    {
        $this->basicContext->getDefaultShopId()->willReturn(self::DEFAULT_SHOP_ID);
        $this->shopStateService->isLaunched()
            ->willReturn(false);
        $this->shopAdapter->themeExists(self::DEFAULT_THEME)
            ->willReturn(false);

        $exitCode = $this->commandTester->execute([
            '--db-host'           => self::HOST,
            '--db-port'           => self::PORT,
            '--db-name'           => self::DB,
            '--db-user'           => self::DB_USER,
            '--db-password'       => self::DB_PASS,
            '--shop-url'          => self::URL,
            '--shop-directory'    => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language'          => self::LANG,
        ]);

        $this->assertServiceCallsWithCompleteArgs();
        $this->assertSame(0, $exitCode);
    }

    public function testExecuteWithMissingOptionalArgs(): void
    {
        $this->basicContext->getDefaultShopId()->willReturn(self::DEFAULT_SHOP_ID);

        $this->shopStateService->isLaunched()
            ->willReturn(false);

        $this->shopAdapter->themeExists(self::DEFAULT_THEME)
            ->willReturn(true);

        $this->shopAdapter->activateTheme(self::DEFAULT_THEME);

        $exitCode = $this->commandTester->execute([
            '--db-host'           => self::HOST,
            '--db-port'           => self::PORT,
            '--db-name'           => self::DB,
            '--db-user'           => self::DB_USER,
            '--db-password'       => self::DB_PASS,
            '--shop-url'          => self::URL,
            '--shop-directory'    => self::DIR,
            '--compile-directory' => self::TMP_DIR,
        ]);

        $this->assertServiceCallsWithOptionalArgs();
        $this->assertSame(0, $exitCode);
    }

    public function testInstallationTimeSaved(): void
    {
        $this->basicContext->getDefaultShopId()->willReturn(self::DEFAULT_SHOP_ID);
        $this->shopStateService->isLaunched()
            ->willReturn(false);
        $this->shopAdapter->themeExists(self::DEFAULT_THEME)
            ->willReturn(false);

        $timeBeforeInstallation = time();

        $this->commandTester->execute([
            '--db-host'           => self::HOST,
            '--db-port'           => self::PORT,
            '--db-name'           => self::DB,
            '--db-user'           => self::DB_USER,
            '--db-password'       => self::DB_PASS,
            '--shop-url'          => self::URL,
            '--shop-directory'    => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language'          => self::LANG,
        ]);

        $shopInstallationTimeSetting =
            $this->get(ShopConfigurationSettingDaoInterface::class)->get('sTagList', self::DEFAULT_SHOP_ID);

        $this->assertGreaterThanOrEqual($timeBeforeInstallation, $shopInstallationTimeSetting->getValue());
    }

    private function createCommand(): Command
    {
        $this->prepareMocks();
        return new ShopSetupCommand(
            $this->databaseChecker->reveal(),
            $this->databaseInstall->reveal(),
            $this->configFileDao->reveal(),
            $this->directoryValidator->reveal(),
            $this->languageInstaller->reveal(),
            $this->htaccessUpdateService->reveal(),
            $this->shopStateService->reveal(),
            $this->shopAdapter->reveal(),
            $this->basicContext->reveal(),
            $this->get(ShopConfigurationSettingDaoInterface::class)
        );
    }

    private function prepareMocks(): void
    {
        $this->databaseChecker = $this->prophesize(DatabaseCheckerInterface::class);
        $this->databaseInstall = $this->prophesize(DatabaseInstallerInterface::class);
        $this->configFileDao = $this->prophesize(ConfigFileDaoInterface::class);
        $this->directoryValidator = $this->prophesize(DirectoryValidatorInterface::class);
        $this->languageInstaller = $this->prophesize(LanguageInstallerInterface::class);
        $this->htaccessUpdateService = $this->prophesize(HtaccessUpdaterInterface::class);
        $this->shopStateService = $this->prophesize(ShopStateServiceInterface::class);
        $this->basicContext = $this->prophesize(BasicContextInterface::class);
        $this->shopAdapter = $this->prophesize(ShopAdapterInterface::class);
    }

    private function assertServiceCallsWithCompleteArgs(): void
    {
        $this->databaseInstall->install(
            self::HOST,
            self::PORT,
            self::DB_USER,
            self::DB_PASS,
            self::DB
        )
            ->shouldHaveBeenCalledOnce();
        $this->configFileDao->replacePlaceholder('sShopURL', self::URL)
            ->shouldHaveBeenCalledOnce();
        $this->configFileDao->replacePlaceholder('sShopDir', self::DIR)
            ->shouldHaveBeenCalledOnce();
        $this->configFileDao->replacePlaceholder('sCompileDir', self::TMP_DIR)
            ->shouldHaveBeenCalledOnce();
        $this->directoryValidator->validateDirectory(self::DIR, self::TMP_DIR)
            ->shouldHaveBeenCalledOnce();
        $this->languageInstaller->install(new DefaultLanguage(self::LANG))
            ->shouldHaveBeenCalledOnce();
        $this->htaccessUpdateService->updateRewriteBaseDirective(self::URL)
            ->shouldHaveBeenCalledOnce();
    }

    private function assertServiceCallsWithOptionalArgs(): void
    {
        $this->languageInstaller->install(new DefaultLanguage(self::DEFAULT_LANG))
            ->shouldHaveBeenCalledOnce();
    }
}
