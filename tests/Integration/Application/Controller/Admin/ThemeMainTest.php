<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsView;
use OxidEsales\EshopCommunity\Application\Controller\Admin\ThemeMain;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ThemeMainTest extends IntegrationTestCase
{
    private const THEME_ID = 'testTheme';
    private const SELF_REFERENCING_THEME_ID = 'selfReferencingTheme';
    private const SHOP_ID = 1;

    public function testSetThemeActivatesTheme(): void
    {
        $this->installTheme(self::THEME_ID);

        $controller = oxNew(ThemeMain::class);
        $controller->setEditObjectId(self::THEME_ID);
        $controller->setTheme();

        $this->assertTrue($this->isThemeActive(self::THEME_ID));
    }

    public function testSetThemeDisplaysErrorForUnknownTheme(): void
    {
        $this->expectDisplayError('EXCEPTION_THEME_NOT_LOADED');

        $controller = oxNew(ThemeMain::class);
        $controller->setEditObjectId('unknownTheme');
        $controller->setTheme();
    }

    public function testSetThemeDisplaysCompatibilityErrorAndDoesNotActivateThemeDeclaringItselfAsItsOwnParent(): void
    {
        $this->installTheme(self::SELF_REFERENCING_THEME_ID);
        $this->expectDisplayError('EXCEPTION_THEME_INHERITANCE_CYCLE');

        $controller = oxNew(ThemeMain::class);
        $controller->setEditObjectId(self::SELF_REFERENCING_THEME_ID);
        $controller->setTheme();

        $this->assertFalse($this->isThemeActive(self::SELF_REFERENCING_THEME_ID));
    }

    private function installTheme(string $themeId): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install(__DIR__ . "/Fixtures/$themeId");
    }

    private function isThemeActive(string $themeId): bool
    {
        return $this->get(ThemeStateServiceInterface::class)->isActive($themeId, self::SHOP_ID);
    }

    private function expectDisplayError(string $translationKey): void
    {
        $utilsView = $this->createMock(UtilsView::class);
        $utilsView->expects($this->once())->method('addErrorToDisplay')->with($translationKey);

        Registry::set(UtilsView::class, $utilsView);
    }
}
