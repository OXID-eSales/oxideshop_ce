<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class ConfigParentThemeFallbackTest extends IntegrationTestCase
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

        Registry::getConfig()->reinitialize();
    }

    public function testResourcePathFallsBackToParentThemeWhenChildThemeLacksTheAsset(): void
    {
        $path = Registry::getConfig()->getResourcePath('test-asset.css');

        $this->assertStringContainsString(
            '/out/' . self::PARENT_THEME_ID . '/src/test-asset.css',
            $path
        );
    }

    public function testResourcePathUsesChildThemeAssetWhenAssetExistsInBothThemes(): void
    {
        $path = Registry::getConfig()->getResourcePath('shared-asset.css');

        $this->assertStringContainsString(
            '/out/' . self::CHILD_THEME_ID . '/src/shared-asset.css',
            $path
        );
    }

    public function testMissingAssetLookupIsFalseInsteadOfFailingWhenActiveThemeMetaDataIsBroken(): void
    {
        $this->pointThemeSourceToBrokenMetaData(self::CHILD_THEME_ID);

        $this->assertFalse(Registry::getConfig()->getDir('missing-asset.css', 'src', false));
    }

    private function pointThemeSourceToBrokenMetaData(string $themeId): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $brokenThemePath = realpath("$this->fixtureDirectory/imageBrokenMetaDataTheme");
        $configuration = $dao->get($themeId, self::SHOP_ID)->setSource(
            Path::makeRelative($brokenThemePath, $this->get(BasicContextInterface::class)->getShopRootPath())
        );

        $dao->save($configuration, self::SHOP_ID);
    }

    private function installTheme(string $themeId): void
    {
        $themePath = realpath("$this->fixtureDirectory/shop/source/Application/views/$themeId");

        $this->get(ThemeConfigurationInstallerInterface::class)->install($themePath);
    }
}
