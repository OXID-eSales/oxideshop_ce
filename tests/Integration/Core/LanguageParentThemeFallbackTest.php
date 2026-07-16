<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class LanguageParentThemeFallbackTest extends IntegrationTestCase
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

    public function testChildThemeInheritsLanguageFileFromParentTheme(): void
    {
        $translation = (new Language())->translateString('TEST_PARENT_THEME_ONLY_KEY', 0);

        $this->assertSame('from parent theme', $translation);
    }

    public function testChildThemeOverridesSharedTranslationKeyFromParentTheme(): void
    {
        $translation = (new Language())->translateString('TEST_SHARED_KEY', 0);

        $this->assertSame('child value', $translation);
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
