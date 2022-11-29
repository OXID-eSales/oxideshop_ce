<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup;

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
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class ShopSetupCommandTest extends TestCase
{
    use ProphecyTrait;

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

    /** @var CommandTester */
    private $commandTester;
    /** @var DatabaseCheckerInterface|ObjectProphecy */
    private $databaseChecker;
    /** @var DatabaseInstallerInterface|ObjectProphecy */
    private $databaseInstall;
    /** @var ConfigFileDaoInterface|ObjectProphecy */
    private $configFileDao;
    /** @var DirectoryValidatorInterface|ObjectProphecy */
    private $directoryValidator;
    /** @var LanguageInstallerInterface|ObjectProphecy */
    private $languageInstaller;
    /** @var HtaccessUpdaterInterface|ObjectProphecy */
    private $htaccessUpdateService;
    /** @var ShopStateServiceInterface|ObjectProphecy */
    private $shopStateService;
    /** @var BasicContextInterface|ObjectProphecy */
    private $basicContext;
    /** @var ShopAdapterInterface|ObjectProphecy */
    private $shopAdapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandTester = new CommandTester($this->createCommand());
    }

    public function testExecuteWithMissingArgs(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandTester->execute([]);
    }

    public function testExecuteWithShopAlreadyLaunched(): void
    {
        $this->shopStateService->isLaunched()
            ->willReturn(true);

        $this->expectException(ShopIsLaunchedException::class);

        $this->commandTester->execute([
            '--db-host' => self::HOST,
            '--db-port' => self::PORT,
            '--db-name' => self::DB . uniqid('_', false),
            '--db-user' => self::DB_USER,
            '--db-password' => self::DB_PASS,
            '--shop-url' => self::URL,
            '--shop-directory' => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language' => self::LANG,
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
            '--db-host' => self::HOST,
            '--db-port' => self::PORT,
            '--db-name' => self::DB . uniqid('_', false),
            '--db-user' => self::DB_USER,
            '--db-password' => self::DB_PASS,
            '--shop-url' => self::URL,
            '--shop-directory' => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language' => self::LANG,
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
            '--db-host' => self::HOST,
            '--db-port' => self::PORT,
            '--db-name' => self::DB,
            '--db-user' => self::DB_USER,
            '--db-password' => self::DB_PASS,
            '--shop-url' => self::URL,
            '--shop-directory' => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language' => self::LANG,
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
            '--db-host' => self::HOST,
            '--db-port' => self::PORT,
            '--db-name' => self::DB,
            '--db-user' => self::DB_USER,
            '--db-password' => self::DB_PASS,
            '--shop-url' => self::URL,
            '--shop-directory' => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--language' => self::LANG,
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
            '--db-host' => self::HOST,
            '--db-port' => self::PORT,
            '--db-name' => self::DB,
            '--db-user' => self::DB_USER,
            '--db-password' => self::DB_PASS,
            '--shop-url' => self::URL,
            '--shop-directory' => self::DIR,
            '--compile-directory' => self::TMP_DIR
        ]);

        $this->assertServiceCallsWithOptionalArgs();
        $this->assertSame(0, $exitCode);
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
            $this->shopAdapter->reveal()
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
