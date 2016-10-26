<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use \OxidEsales\EshopCommunity\Core\Theme;

use \Exception;
use \oxTestModules;

/**
 * Tests for Shop_Config class
 */
class ThemeMainTest extends \OxidTestCase
{

    /**
     * Theme_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('Theme_Main');
        $this->assertEquals('theme_main.tpl', $oView->render());

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oTheme']));
        $this->assertTrue($aViewData['oTheme'] instanceof Theme);
        $this->assertEquals('azure', $aViewData['oTheme']->getInfo('id'));
    }


    public function testSetTheme()
    {
        $oTM = $this->getMock('Theme_Main', array('getEditObjectId'));
        $oTM->expects($this->any())->method('getEditObjectId')->will($this->returnValue('azure'));

        oxTestModules::addFunction('oxTheme', 'load($name)', '{if ($name != "azure") throw new Exception("FAIL TO LOAD"); return true;}');
        oxTestModules::addFunction('oxTheme', 'activate', '{throw new Exception("OK");}');

        try {
            $oTM->setTheme();
            $this->fail('should have called overriden activate');
        } catch (Exception $e) {
            $this->assertEquals('OK', $e->getMessage());
        }
    }

    /**
     * Test if theme in config checking was called.
     */
    public function testThemeConfigExceptionInRender()
    {
        $oTM = $this->getMock('Theme_Main', array('themeInConfigFile'));
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
        $oView->setConfig($oConfig);
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
        $oView->setConfig($oConfig);
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
        $oView->setConfig($oConfig);
        $this->assertEquals(true, $oView->themeInConfigFile(), 'Should return true as there is sTheme and sCustomTheme.');
    }
}
