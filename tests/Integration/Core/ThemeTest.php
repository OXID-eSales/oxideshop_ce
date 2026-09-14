<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Theme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class ThemeTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;
    private const THEME_ID = 'mismatchedTheme';

    private string $fixtureDirectory = __DIR__ . '/Fixtures';

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.shop_source_directory', "$this->fixtureDirectory/shop/source/");

        $this->installTheme(self::THEME_ID);
    }

    public function testLoadFailsWhenMetadataIdDoesNotMatchRequestedThemeId(): void
    {
        $theme = oxNew(Theme::class);

        $this->assertFalse($theme->load(self::THEME_ID));
    }

    public function testGetListReturnsOnlyThemesWithSavedConfiguration(): void
    {
        $this->installTheme('childTheme');

        $themeList = oxNew(Theme::class)->getList();

        $this->assertArrayHasKey('childTheme', $themeList);
        $this->assertArrayNotHasKey('parentTheme', $themeList);
    }

    private function installTheme(string $themeId): void
    {
        $context = $this->get(BasicContextInterface::class);
        $themePath = realpath("$this->fixtureDirectory/shop/source/Application/views/$themeId");

        $configuration = (new ThemeConfiguration())
            ->setId($themeId)
            ->setSource(Path::makeRelative($themePath, $context->getShopRootPath()));
        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, self::SHOP_ID);
    }
}
