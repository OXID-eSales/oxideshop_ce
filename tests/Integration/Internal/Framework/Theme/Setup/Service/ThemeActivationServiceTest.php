<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ThemeActivationServiceTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;
    private const PARENT_THEME_ID = 'apexParent';
    private const CHILD_THEME_ID = 'incompatibleChild';

    private string $fixtureDirectory = __DIR__ . '/Fixtures';

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.shop_source_directory', "$this->fixtureDirectory/shop/source/");

        $this->installTheme(self::PARENT_THEME_ID);
        $this->installTheme(self::CHILD_THEME_ID);
    }

    public function testActivateRejectsChildThemeWithIncompatibleParentVersion(): void
    {
        $this->expectException(ThemeParentVersionMismatchException::class);

        try {
            $this->get(ThemeActivationServiceInterface::class)->activate(self::CHILD_THEME_ID, self::SHOP_ID);
        } finally {
            $this->assertFalse(
                $this->get(ThemeStateServiceInterface::class)->isActive(self::CHILD_THEME_ID, self::SHOP_ID)
            );
        }
    }

    private function installTheme(string $themeId): void
    {
        $themePath = realpath("$this->fixtureDirectory/shop/source/Application/views/$themeId");

        $this->get(ThemeConfigurationInstallerInterface::class)->install($themePath);
    }
}