<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\Exception\ThemeParentNotInstalledException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ThemeActivationServiceTest extends IntegrationTestCase
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
    }

    public function testValidateActivatableThrowsThemeParentNotInstalledExceptionWhenParentIsRemovedAfterActivation(): void
    {
        $installer = $this->get(ThemeConfigurationInstallerInterface::class);
        $parentThemePath = "$this->fixtureDirectory/shop/source/Application/views/" . self::PARENT_THEME_ID;
        $installer->install($parentThemePath);
        $installer->install("$this->fixtureDirectory/shop/source/Application/views/" . self::CHILD_THEME_ID);

        $themeActivationService = $this->get(ThemeActivationServiceInterface::class);
        $themeActivationService->activate(self::CHILD_THEME_ID, self::SHOP_ID);

        $installer->uninstall($parentThemePath);

        $this->expectException(ThemeParentNotInstalledException::class);

        $themeActivationService->validateActivatable(self::CHILD_THEME_ID, self::SHOP_ID);
    }
}
