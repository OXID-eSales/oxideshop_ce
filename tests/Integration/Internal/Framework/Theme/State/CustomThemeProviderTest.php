<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\CustomThemeProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\CustomThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class CustomThemeProviderTest extends IntegrationTestCase
{
    private const SHOP_ID = 1;
    private const PARENT_THEME_ID = 'apex';
    private const CHILD_THEME_ID = 'testChildTheme';

    public function testHasCustomThemeReturnsTrueWhenChildThemeIsActive(): void
    {
        $this->installChildTheme();
        $this->activate(self::CHILD_THEME_ID);

        $this->assertTrue($this->get(CustomThemeProviderInterface::class)->hasCustomTheme(self::SHOP_ID));
    }

    public function testGetCustomThemeIdReturnsChildThemeIdWhenActive(): void
    {
        $this->installChildTheme();
        $this->activate(self::CHILD_THEME_ID);

        $this->assertSame(
            self::CHILD_THEME_ID,
            $this->get(CustomThemeProviderInterface::class)->getCustomThemeId(self::SHOP_ID)
        );
    }

    public function testHasCustomThemeReturnsFalseWhenChildThemeIsOnlyInstalled(): void
    {
        $this->installParentTheme();
        $this->installChildTheme();
        $this->activate(self::PARENT_THEME_ID);

        $this->assertFalse($this->get(CustomThemeProviderInterface::class)->hasCustomTheme(self::SHOP_ID));
    }

    public function testGetCustomThemeIdThrowsWhenChildThemeIsOnlyInstalled(): void
    {
        $this->installParentTheme();
        $this->installChildTheme();
        $this->activate(self::PARENT_THEME_ID);

        $this->expectException(CustomThemeNotFoundException::class);

        $this->get(CustomThemeProviderInterface::class)->getCustomThemeId(self::SHOP_ID);
    }

    private function installParentTheme(): void
    {
        $context = $this->get(BasicContextInterface::class);
        $themePath = Path::join($context->getShopRootPath(), 'source', 'Application', 'views', self::PARENT_THEME_ID);

        $configuration = (new ThemeConfiguration())
            ->setId(self::PARENT_THEME_ID)
            ->setSource(Path::makeRelative($themePath, $context->getShopRootPath()));

        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, self::SHOP_ID);
    }

    private function installChildTheme(): void
    {
        $context = $this->get(BasicContextInterface::class);
        $themePath = realpath(__DIR__ . '/Fixtures/testChildTheme');

        $configuration = (new ThemeConfiguration())
            ->setId(self::CHILD_THEME_ID)
            ->setSource(Path::makeRelative($themePath, $context->getShopRootPath()));

        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, self::SHOP_ID);
    }

    private function activate(string $themeId): void
    {
        $this->get(ThemeActivationServiceInterface::class)->activate($themeId, self::SHOP_ID);
    }
}
