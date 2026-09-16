<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Controller\Admin\ThemeList;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\View\ThemeView;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ThemeListTest extends IntegrationTestCase
{
    private const THEME_ID = 'testTheme';
    private const SHOP_ID = 1;

    public function testRenderListsInstalledThemesWithActiveState(): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install(__DIR__ . '/Fixtures/' . self::THEME_ID);
        $this->get(ThemeActivationServiceInterface::class)->activate(self::THEME_ID, self::SHOP_ID);

        $controller = $this->get(ThemeList::class);
        $template = $controller->render();
        $themes = $controller->getViewDataElement('mylist');

        $this->assertSame('theme_list', $template);
        $this->assertContainsOnlyInstancesOf(ThemeView::class, $themes);
        $this->assertArrayHasKey(self::THEME_ID, $themes);
        $this->assertSame('Test Theme', $themes[self::THEME_ID]->getTitle());
        $this->assertTrue($themes[self::THEME_ID]->isActive());
    }
}
