<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

/**
 * Tests for Shop_Config class
 */
class ThemeConfigTest extends \OxidTestCase
{

    /**
     * Shop_Config::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $oView = oxNew('Theme_Config');
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
        $oTheme_Config = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ThemeConfiguration::class, array('getEditObjectId'));
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
        $iShopId = 125;
        $sName = 'someName';
        $sValue = 'someValue';
        $sThemeName = 'testtheme';

        // Check if saveShopConfVar is called with correct values.
        $aParams = array($sName => $sValue);

        /** @var oxConfig|PHPUnit\Framework\MockObject\MockObject $oConfig */
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getShopId', 'getRequestParameter', 'saveShopConfVar', '_loadVarsFromDb'));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue($iShopId));
        $oConfig->expects($this->any())->method('getRequestParameter')->will($this->returnValue($aParams));
        $oConfig->expects($this->any())->method('_loadVarsFromDb')->will($this->returnValue(true));
        $oConfig->setConfigParam('blClearCacheOnLogout', true);

        $valueMap = array(
            array('bool', $sName, $sValue, $iShopId, 'theme:' . $sThemeName, true),
            array('str', $sName, $sValue, $iShopId, 'theme:' . $sThemeName, true),
            array('arr', $sName, $sValue, $iShopId, 'theme:' . $sThemeName, true),
            array('aarr', $sName, $sValue, $iShopId, 'theme:' . $sThemeName, true),
            array('select', $sName, $sValue, $iShopId, 'theme:' . $sThemeName, true),
        );
        $oConfig->expects($this->exactly(6))->method('saveShopConfVar')->will($this->returnValueMap($valueMap));

        /** @var Theme_Config|PHPUnit\Framework\MockObject\MockObject $oTheme_Config */
        $oTheme_Config = $this->getMock(
            'Theme_Config',
            array('getEditObjectId', '_serializeConfVar'),
            array(),
            '',
            false
        );
        $oTheme_Config->expects($this->once())->method('getEditObjectId')->will($this->returnValue($sThemeName));
        $oTheme_Config->expects($this->atLeastOnce())->method('_serializeConfVar')->will($this->returnValue($sValue));
        $oTheme_Config->setConfig($oConfig);

        $oTheme_Config->saveConfVars();
    }
}
