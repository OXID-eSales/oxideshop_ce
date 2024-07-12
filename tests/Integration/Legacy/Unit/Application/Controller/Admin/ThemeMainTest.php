<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Theme;
use \Exception;
use \oxTestModules;

/**
 * Tests for Shop_Config class
 */
class ThemeMainTest extends \OxidTestCase
{

    /**
     * Theme_Main::Render() test case
     */
    public function testRender()
    {
        $this->getConfig()->setConfigParam('sTheme', 'azure');

        // testing..
        $oView = oxNew('Theme_Main');
        $this->assertEquals('theme_main', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oTheme']));
        $this->assertTrue($aViewData['oTheme'] instanceof Theme);
        $this->assertEquals('azure', $aViewData['oTheme']->getInfo('id'));
    }


    public function testSetTheme()
    {
        $oTM = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ThemeMain::class, ['getEditObjectId']);
        $oTM->expects($this->any())->method('getEditObjectId')->will($this->returnValue('azure'));

        oxTestModules::addFunction('oxTheme', 'load($name)', '{if ($name != "azure") throw new Exception("FAIL TO LOAD"); return true;}');
        oxTestModules::addFunction('oxTheme', 'activate', '{throw new Exception("OK");}');

        try {
            $oTM->setTheme();
            $this->fail('should have called overriden activate');
        } catch (Exception $exception) {
            $this->assertEquals('OK', $exception->getMessage());
        }
    }

    /**
     * Test if theme in config checking was called.
     */
    public function testThemeConfigExceptionInRender()
    {
        $oTM = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ThemeMain::class, ['themeInConfigFile']);
        $oTM->expects($this->once())->method('themeInConfigFile');
        $oTM->render();
    }

    /**
     * Check if theme checking works correct.
     */
    public function testThemeConfigException()
    {
        $oView = oxNew('Theme_Main');
        $this->assertEquals(false, $oView->themeInConfigFile(), 'Should not be theme in config file by default.');
    }

    /**
     * Check if theme checking works correct when only sTheme is set in config.
     */
    public function testThemeConfigExceptionSTheme()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->sTheme = 'azure';
        $oConfig->sCustomTheme = null;

        $oView = oxNew('Theme_Main');
        Registry::set(Config::class, $oConfig);
        $this->assertEquals(true, $oView->themeInConfigFile(), 'Should return true as there is sTheme.');
    }

    /**
     * Check if theme checking works correct when only sCustomTheme is set in config.
     */
    public function testThemeConfigExceptionSCustomTheme()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->sTheme = null;
        $oConfig->sCustomTheme = 'someTheme';

        $oView = oxNew('Theme_Main');
        Registry::set(Config::class, $oConfig);
        $this->assertEquals(true, $oView->themeInConfigFile(), 'Should return true as there is sCustomTheme.');
    }

    /**
     * Check if theme checking works correct when sTheme and sCustomTheme is set in config.
     */
    public function testThemeConfigExceptionSThemeSCustomTheme()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->sTheme = 'azure';
        $oConfig->sCustomTheme = 'someTheme';

        $oView = oxNew('Theme_Main');
        Registry::set(Config::class, $oConfig);
        $this->assertEquals(true, $oView->themeInConfigFile(), 'Should return true as there is sTheme and sCustomTheme.');
    }
}
