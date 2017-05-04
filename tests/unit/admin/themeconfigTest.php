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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for Shop_Config class
 */
class Unit_Admin_ThemeConfigTest extends OxidTestCase
{

    /**
     * Shop_Config::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oView = new Theme_Config();
        $this->assertEquals('theme_config.tpl', $oView->render());
    }

    /**
     * Shop_Config::testGetModuleForConfigVars() test case
     *
     * @return null
     */
    public function testGetModuleForConfigVars()
    {
        $sThemeName = 'testtheme';
        $oTheme_Config = $this->getMock('Theme_Config', array('getEditObjectId'));
        $oTheme_Config->expects($this->any())->method('getEditObjectId')->will($this->returnValue($sThemeName));
        $this->assertEquals('theme:' . $sThemeName, $oTheme_Config->UNITgetModuleForConfigVars());
    }

    /**
     * Shop_Config::testSaveConfVars() test case
     *
     * @return null
     */
    public function testSaveConfVars()
    {
        // Params from oxConfig as it is used in theme_config.
//        $_aConfParams = array(
//            "bool"   => 'confbools',
//            "str"    => 'confstrs',
//            "arr"    => 'confarrs',
//            "aarr"   => 'confaarrs',
//            "select" => 'confselects',
//            "num"    => 'confnum',
//        );

        $iShopId = 125;
        $sName = 'someName';
        $sValue = 'someValue';
        $sThemeName = 'testtheme';

        // Check if saveShopConfVar is called with correct values.
        $aParams = array($sName => $sValue);
        $oConfig = $this->getMock('oxConfig', array('getShopId', 'getRequestParameter', 'saveShopConfVar'));
        $oConfig->expects($this->atLeastOnce())->method('getShopId')->will($this->returnValue($iShopId));
        $oConfig->expects($this->atLeastOnce())->method('getRequestParameter')->will($this->returnValue($aParams));
        $oConfig->expects($this->at(3))->method('saveShopConfVar')->with(
            'bool', $sName, $sValue, $iShopId, 'theme:' . $sThemeName
        )->will($this->returnValue(true));
        $oConfig->expects($this->at(5))->method('saveShopConfVar')->with(
            'str', $sName, $sValue, $iShopId, 'theme:' . $sThemeName
        )->will($this->returnValue(true));
        $oConfig->expects($this->at(7))->method('saveShopConfVar')->with(
            'arr', $sName, $sValue, $iShopId, 'theme:' . $sThemeName
        )->will($this->returnValue(true));
        $oConfig->expects($this->at(9))->method('saveShopConfVar')->with(
            'aarr', $sName, $sValue, $iShopId, 'theme:' . $sThemeName
        )->will($this->returnValue(true));
        $oConfig->expects($this->at(11))->method('saveShopConfVar')->with(
            'select', $sName, $sValue, $iShopId, 'theme:' . $sThemeName
        )->will($this->returnValue(true));
        $oConfig->expects($this->at(13))->method('saveShopConfVar')->with(
            'num', $sName, $sValue, $iShopId, 'theme:' . $sThemeName
        )->will($this->returnValue(true));


        $oTheme_Config = $this->getMock(
            'Theme_Config', array('getConfig', 'getEditObjectId', '_serializeConfVar')
            , array(), '', false
        );
        $oTheme_Config->expects($this->atLeastOnce())->method('getConfig')->will($this->returnValue($oConfig));
        $oTheme_Config->expects($this->once())->method('getEditObjectId')->will($this->returnValue($sThemeName));
        $oTheme_Config->expects($this->atLeastOnce())->method('_serializeConfVar')->will($this->returnValue($sValue));

        $oTheme_Config->saveConfVars();
    }

}
