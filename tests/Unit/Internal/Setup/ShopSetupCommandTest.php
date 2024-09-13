<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Database\ShopDbManagerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Directory\DirectoryValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\HtaccessUpdaterInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Htaccess\ShopBaseUrl;
use OxidEsales\EshopCommunity\Internal\Setup\Language\DefaultLanguage;
use OxidEsales\EshopCommunity\Internal\Setup\Language\LanguageInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Parameters\SetupParameters;
use OxidEsales\EshopCommunity\Internal\Setup\Parameters\SetupParametersFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ShopConfiguration\ShopConfigurationUpdaterInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ShopSetupCommand;
use OxidEsales\EshopCommunity\Internal\Setup\Validator\SetupInfrastructureValidatorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument\Token\TypeToken;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class ShopSetupCommandTest extends TestCase
{
    use ProphecyTrait;

    private ShopDbManagerInterface|ObjectProphecy $shopDbManager;
    private HtaccessUpdaterInterface|ObjectProphecy $htaccessUpdateService;
    private ObjectProphecy|DirectoryValidatorInterface $directoryValidator;
    private ObjectProphecy|LanguageInstallerInterface $languageInstaller;
    private ObjectProphecy|SetupInfrastructureValidatorInterface $SetupInfrastructureValidator;
    private SetupParametersFactoryInterface|ObjectProphecy $setupParametersFactory;
    private ShopConfigurationUpdaterInterface|ObjectProphecy $shopConfigurationUpdater;

    public function testExecute(): void
    {
        $commandTester = new CommandTester($this->createCommand());

        $commandTester->execute([]);

        $this->setupParametersFactory
            ->create(new TypeToken(InputInterface::class))
            ->shouldHaveBeenCalledOnce();
        $this->SetupInfrastructureValidator
            ->validate(new TypeToken(SetupParameters::class))
            ->shouldHaveBeenCalledOnce();
        $this->shopDbManager
            ->create(new TypeToken(DatabaseConfiguration::class))
            ->shouldHaveBeenCalledOnce();
        $this->languageInstaller
            ->install(new TypeToken(DefaultLanguage::class))
            ->shouldHaveBeenCalledOnce();
        $this->htaccessUpdateService
            ->updateRewriteBaseDirective(new TypeToken(ShopBaseUrl::class))
            ->shouldHaveBeenCalledOnce();
        $this->shopConfigurationUpdater
            ->saveShopSetupTime()
            ->shouldHaveBeenCalledOnce();
        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
    }

    private function createCommand(): Command
    {
        $this->prepareMocks();
        return new ShopSetupCommand(
            $this->setupParametersFactory->reveal(),
            $this->SetupInfrastructureValidator->reveal(),
            $this->shopDbManager->reveal(),
            $this->languageInstaller->reveal(),
            $this->htaccessUpdateService->reveal(),
            $this->shopConfigurationUpdater->reveal(),
            'language',
        );
    }

    private function prepareMocks(): void
    {
        $this->setupParametersFactory = $this->prophesize(SetupParametersFactoryInterface::class);
        $this->SetupInfrastructureValidator = $this->prophesize(SetupInfrastructureValidatorInterface::class);
        $this->shopDbManager = $this->prophesize(ShopDbManagerInterface::class);
        $this->languageInstaller = $this->prophesize(LanguageInstallerInterface::class);
        $this->htaccessUpdateService = $this->prophesize(HtaccessUpdaterInterface::class);
        $this->shopConfigurationUpdater = $this->prophesize(ShopConfigurationUpdaterInterface::class);

        $setupParameters = $this->prophesize(SetupParameters::class);
        $setupParameters
            ->getShopBaseUrl()
            ->willReturn(new ShopBaseUrl('https://localhost.local'));
        $setupParameters
            ->getDbConfig()
            ->willReturn(new DatabaseConfiguration(
                getenv('OXID_DB_URL')
            ));
        $setupParameters
            ->getLanguage()
            ->willReturn(new DefaultLanguage('de'));
        $this->setupParametersFactory
            ->create(new TypeToken(InputInterface::class))
            ->willReturn($setupParameters);
    }
}
