<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop_Config class
 */
class ThemeConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Shop_Config::Render() test case
     */
    public function testRender()
    {
        $oView = oxNew('Theme_Config');
        $this->assertSame('theme_config', $oView->render());
    }

    /**
     * Shop_Config::testGetModuleForConfigVars() test case
     */
    public function testGetModuleForConfigVars()
    {
        $sThemeName = 'testtheme';
        $oTheme_Config = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ThemeConfiguration::class, ['getEditObjectId']);
        $oTheme_Config->method('getEditObjectId')->willReturn($sThemeName);
        $this->assertSame('theme:' . $sThemeName, $oTheme_Config->getModuleForConfigVars());
    }
}
