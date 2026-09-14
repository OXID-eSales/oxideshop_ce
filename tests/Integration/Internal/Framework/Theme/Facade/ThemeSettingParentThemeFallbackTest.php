<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ThemeSettingParentThemeFallbackTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;
    private const PARENT_THEME_ID = 'parentTheme';
    private const CHILD_THEME_ID = 'childTheme';

    private string $fixtureDirectory = __DIR__ . '/Fixtures';

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.shop_source_directory', "$this->fixtureDirectory/shop/source/");

        $this->installTheme(self::PARENT_THEME_ID);
        $this->installTheme(self::CHILD_THEME_ID);
        $this->get(ThemeActivationServiceInterface::class)->activate(self::CHILD_THEME_ID, self::SHOP_ID);
    }

    public function testChildThemeInheritsSettingNotDeclaredByChildFromParentTheme(): void
    {
        $this->saveSetting(self::PARENT_THEME_ID, 'iconSize', 'str', '87*87');

        $this->assertSame('87*87', $this->get(ThemeSettingServiceInterface::class)->getString('iconSize'));
    }

    public function testChildThemeOverridesSettingDeclaredByParentTheme(): void
    {
        $this->saveSetting(self::PARENT_THEME_ID, 'iconSize', 'str', '87*87');
        $this->saveSetting(self::CHILD_THEME_ID, 'iconSize', 'str', '100*100');

        $this->assertSame('100*100', $this->get(ThemeSettingServiceInterface::class)->getString('iconSize'));
    }

    public function testExistsReturnsTrueForSettingOnlyDeclaredByParentTheme(): void
    {
        $this->saveSetting(self::PARENT_THEME_ID, 'iconSize', 'str', '87*87');

        $this->assertTrue($this->get(ThemeSettingServiceInterface::class)->exists('iconSize'));
    }

    private function saveSetting(string $themeId, string $name, string $type, mixed $value): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $configuration = $dao->get($themeId, self::SHOP_ID);
        $configuration->addThemeSetting((new Setting())->setName($name)->setType($type)->setValue($value));
        $dao->save($configuration, self::SHOP_ID);
    }

    private function installTheme(string $themeId): void
    {
        $themePath = realpath("$this->fixtureDirectory/shop/source/Application/views/$themeId");

        $this->get(ThemeConfigurationInstallerInterface::class)->install($themePath);
    }
}
