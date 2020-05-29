<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Service\AdminUserServiceInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\ConfigFileDaoInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\Service\DirectoryValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessUpdateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;
use OxidEsales\EshopCommunity\Internal\Setup\Language\LanguageInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ShopSetupCommand;
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
    private const ADMIN_EMAIL = 'someone@test.com';
    private const ADMIN_PASS = 'some-admin-pass';
    private const LANG = 'de';
    private const DEFAULT_LANG = 'en';
    private const DEFAULT_SHOP_ID = 12345;

    /** @var CommandTester */
    private $commandTester;
    /** @var DatabaseInstallerInterface|ObjectProphecy */
    private $databaseInstall;
    /** @var ConfigFileDaoInterface|ObjectProphecy */
    private $configFileDao;
    /** @var DirectoryValidatorInterface|ObjectProphecy */
    private $directoryValidator;
    /** @var LanguageInstallerInterface|ObjectProphecy */
    private $languageInstaller;
    /** @var HtaccessUpdateServiceInterface|ObjectProphecy */
    private $htaccessUpdateService;
    /** @var AdminUserServiceInterface|ObjectProphecy */
    private $adminUserService;
    /** @var BasicContextInterface|ObjectProphecy */
    private $basicContext;

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

    public function testExecuteWithCompleteArgs(): void
    {
        $this->basicContext->getDefaultShopId()->willReturn(self::DEFAULT_SHOP_ID);

        $act = $this->commandTester->execute([
            '--db-host' => self::HOST,
            '--db-port' => self::PORT,
            '--db-name' => self::DB,
            '--db-user' => self::DB_USER,
            '--db-password' => self::DB_PASS,
            '--shop-url' => self::URL,
            '--shop-directory' => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--admin-email' => self::ADMIN_EMAIL,
            '--admin-password' => self::ADMIN_PASS,
            '--language' => self::LANG,
        ]);

        $this->assertServiceCallsWithCompleteArgs();
        $this->assertSame(0, $act);
    }

    public function testExecuteWithMissingOptionalArgs(): void
    {
        $this->basicContext->getDefaultShopId()->willReturn(self::DEFAULT_SHOP_ID);

        $act = $this->commandTester->execute([
            '--db-host' => self::HOST,
            '--db-port' => self::PORT,
            '--db-name' => self::DB,
            '--db-user' => self::DB_USER,
            '--db-password' => self::DB_PASS,
            '--shop-url' => self::URL,
            '--shop-directory' => self::DIR,
            '--compile-directory' => self::TMP_DIR,
            '--admin-email' => self::ADMIN_EMAIL,
            '--admin-password' => self::ADMIN_PASS,
        ]);

        $this->assertServiceCallsWithOptionalArgs();
        $this->assertSame(0, $act);
    }

    private function createCommand(): Command
    {
        $this->prepareMocks();
        return new ShopSetupCommand(
            $this->databaseInstall->reveal(),
            $this->configFileDao->reveal(),
            $this->directoryValidator->reveal(),
            $this->languageInstaller->reveal(),
            $this->htaccessUpdateService->reveal(),
            $this->adminUserService->reveal(),
            $this->basicContext->reveal()
        );
    }

    private function prepareMocks(): void
    {
        $this->databaseInstall = $this->prophesize(DatabaseInstallerInterface::class);
        $this->configFileDao = $this->prophesize(ConfigFileDaoInterface::class);
        $this->directoryValidator = $this->prophesize(DirectoryValidatorInterface::class);
        $this->languageInstaller = $this->prophesize(LanguageInstallerInterface::class);
        $this->htaccessUpdateService = $this->prophesize(HtaccessUpdateServiceInterface::class);
        $this->adminUserService = $this->prophesize(AdminUserServiceInterface::class);
        $this->basicContext = $this->prophesize(BasicContextInterface::class);
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
        $this->adminUserService->createAdmin(
            self::ADMIN_EMAIL,
            self::ADMIN_PASS,
            Admin::MALL_ADMIN,
            self::DEFAULT_SHOP_ID
        )
            ->shouldHaveBeenCalledOnce();
    }

    private function assertServiceCallsWithOptionalArgs(): void
    {
        $this->languageInstaller->install(new DefaultLanguage(self::DEFAULT_LANG))
            ->shouldHaveBeenCalledOnce();
    }
}
