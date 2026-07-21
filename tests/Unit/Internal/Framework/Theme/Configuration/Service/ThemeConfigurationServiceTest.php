<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeEnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\EnvironmentOverriddenSettingException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationService;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

final class ThemeConfigurationServiceTest extends TestCase
{
    private const THEME_ID = 'testTheme';
    private const SHOP_ID = 1;

    public function testGetConfigurationReturnsConfigurationFromDao(): void
    {
        $configuration = (new ThemeConfiguration())->setId(self::THEME_ID);
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->with(self::THEME_ID, self::SHOP_ID)->willReturn($configuration);

        $this->assertSame(
            $configuration,
            $this->createService($dao)->getConfiguration(self::THEME_ID)
        );
    }

    public function testGetActiveConfigurationReturnsConfigurationOfActiveTheme(): void
    {
        $configuration = (new ThemeConfiguration())->setId(self::THEME_ID);
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->with(self::THEME_ID, self::SHOP_ID)->willReturn($configuration);

        $this->assertSame(
            $configuration,
            $this->createService($dao)->getActiveConfiguration()
        );
    }

    public function testUpdateSettingsAppliesValuesAndSavesConfiguration(): void
    {
        $configuration = (new ThemeConfiguration())
            ->setId(self::THEME_ID)
            ->addThemeSetting((new Setting())->setName('sIconSize')->setType('str')->setValue('100*100'))
            ->addThemeSetting((new Setting())->setName('blShowVouchers')->setType('bool')->setValue(true));

        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->once())->method('save')->with($configuration, self::SHOP_ID);

        $this->createService($dao)->updateSettings($configuration, [
            'sIconSize' => '200*200',
            'blShowVouchers' => false,
        ]);

        $this->assertSame('200*200', $configuration->getSettingByName('sIconSize')->getValue());
        $this->assertFalse($configuration->getSettingByName('blShowVouchers')->getValue());
    }

    public function testUpdateSettingsIgnoresUnknownSettingNames(): void
    {
        $configuration = (new ThemeConfiguration())
            ->setId(self::THEME_ID)
            ->addThemeSetting((new Setting())->setName('sIconSize')->setType('str')->setValue('100*100'));

        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->once())->method('save')->with($configuration, self::SHOP_ID);

        $this->createService($dao)->updateSettings($configuration, ['unknownSetting' => 'value']);

        $this->assertSame('100*100', $configuration->getSettingByName('sIconSize')->getValue());
        $this->assertNull($configuration->getSettingByName('unknownSetting'));
    }

    public function testGetEnvironmentSettingValuesReturnsValuesFromCurrentShop(): void
    {
        $environmentConfigurationDao = $this->createMock(ThemeEnvironmentConfigurationDaoInterface::class);
        $environmentConfigurationDao
            ->expects($this->once())
            ->method('get')
            ->with(self::THEME_ID, self::SHOP_ID)
            ->willReturn(new ThemeEnvironmentConfiguration(['sIconSize' => '200*200']));

        $this->assertSame(
            ['sIconSize' => '200*200'],
            $this->createService(
                $this->createStub(ThemeConfigurationDaoInterface::class),
                $environmentConfigurationDao
            )->getEnvironmentSettingValues(self::THEME_ID)
        );
    }

    public function testUpdateSettingsRejectsEnvironmentOverriddenSettingsWithoutSaving(): void
    {
        $configuration = (new ThemeConfiguration())
            ->setId(self::THEME_ID)
            ->addThemeSetting((new Setting())->setName('sIconSize')->setType('str')->setValue('100*100'));

        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->never())->method('save');

        $environmentConfigurationDao = $this->createStub(ThemeEnvironmentConfigurationDaoInterface::class);
        $environmentConfigurationDao
            ->method('get')
            ->willReturn(new ThemeEnvironmentConfiguration(['sIconSize' => '300*300']));

        $this->expectException(EnvironmentOverriddenSettingException::class);
        $this->expectExceptionMessage(
            "The settings 'sIconSize' of theme 'testTheme' are overridden by the environment configuration"
        );

        $this->createService($dao, $environmentConfigurationDao)
            ->updateSettings($configuration, ['sIconSize' => '200*200']);
    }

    private function createService(
        ThemeConfigurationDaoInterface $dao,
        ?ThemeEnvironmentConfigurationDaoInterface $environmentConfigurationDao = null,
    ): ThemeConfigurationService {
        $themeStateService = $this->createStub(ThemeStateServiceInterface::class);
        $themeStateService->method('getActiveThemeId')->willReturn(self::THEME_ID);

        $context = $this->createStub(ContextInterface::class);
        $context->method('getCurrentShopId')->willReturn(self::SHOP_ID);

        if ($environmentConfigurationDao === null) {
            $environmentConfigurationDao = $this->createStub(ThemeEnvironmentConfigurationDaoInterface::class);
            $environmentConfigurationDao
                ->method('get')
                ->willReturn(new ThemeEnvironmentConfiguration());
        }

        return new ThemeConfigurationService(
            $dao,
            $environmentConfigurationDao,
            $themeStateService,
            $context
        );
    }
}
