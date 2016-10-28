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
namespace Unit\Core;

use \oxTheme;

use \another;

class ThemeTest extends \OxidTestCase
{

    public function setup()
    {
        parent::setUp();
    }

    public function testLoadAndgetInfo()
    {
        $oTheme = $this->getProxyClass('oxTheme');
        $this->assertTrue($oTheme->load('azure'));

        foreach (array('id', 'title', 'description', 'thumbnail', 'version', 'author', 'active') as $key) {
            $this->assertNotNull($oTheme->getInfo($key));
        }
        $this->assertNull($oTheme->getInfo('asdasdasd'));
        $this->assertEquals('azure', $oTheme->getInfo('id'));
    }

    public function testGetList()
    {
        // Count themes in themes folder except admin
        $iCount = count(glob(oxPATH . "/Application/views/*", GLOB_ONLYDIR)) - 1;

        $aThemeList = $this->getProxyClass('oxTheme')->getList();

        $this->assertEquals($iCount, count($aThemeList));
        foreach ($aThemeList as $oTheme) {
            $this->assertTrue($oTheme instanceof \OxidEsales\EshopCommunity\Core\Theme);
        }
    }

    public function testActivateError()
    {
        $oTheme = $this->getMock('oxTheme', array('checkForActivationErrors'));
        $oTheme->expects($this->once())->method('checkForActivationErrors')->will($this->returnValue('Error Message'));
        $this->setExpectedException('oxException', 'Error Message');
        $oTheme->activate();
    }

    public function testActivateMain()
    {
        $oConfig = $this->getMock('stdClass', array('saveShopConfVar'));
        $oConfig->expects($this->at(0))->method('saveShopConfVar')
            ->with(
                $this->equalTo('str'),
                $this->equalTo('sTheme'),
                $this->equalTo('currentT')
            )
            ->will($this->returnValue(null));

        $oConfig->expects($this->at(1))->method('saveShopConfVar')
            ->with(
                $this->equalTo('str'),
                $this->equalTo('sCustomTheme'),
                $this->equalTo('')
            )
            ->will($this->returnValue(null));
        $oTheme = $this->getMock('oxTheme', array('checkForActivationErrors', 'getConfig', 'getInfo'));
        $oTheme->expects($this->at(0))->method('checkForActivationErrors')->will($this->returnValue(false));
        $oTheme->expects($this->at(1))->method('getInfo')->with($this->equalTo('parentTheme'))->will($this->returnValue(''));
        $oTheme->expects($this->at(2))->method('getConfig')->will($this->returnValue($oConfig));
        $oTheme->expects($this->at(3))->method('getInfo')->with($this->equalTo('id'))->will($this->returnValue('currentT'));
        $oTheme->expects($this->at(4))->method('getConfig')->will($this->returnValue($oConfig));
        $oTheme->activate();
    }

    public function testActivateChild()
    {
        $oConfig = $this->getMock('stdClass', array('saveShopConfVar'));
        $oConfig->expects($this->at(0))->method('saveShopConfVar')
            ->with(
                $this->equalTo('str'),
                $this->equalTo('sTheme'),
                $this->equalTo('parentT')
            )
            ->will($this->returnValue(null));

        $oConfig->expects($this->at(1))->method('saveShopConfVar')
            ->with(
                $this->equalTo('str'),
                $this->equalTo('sCustomTheme'),
                $this->equalTo('currentT')
            )
            ->will($this->returnValue(null));
        $oTheme = $this->getMock('oxTheme', array('checkForActivationErrors', 'getConfig', 'getInfo'));
        $oTheme->expects($this->at(0))->method('checkForActivationErrors')->will($this->returnValue(false));
        $oTheme->expects($this->at(1))->method('getInfo')->with($this->equalTo('parentTheme'))->will($this->returnValue('parentT'));
        $oTheme->expects($this->at(2))->method('getConfig')->will($this->returnValue($oConfig));
        $oTheme->expects($this->at(3))->method('getConfig')->will($this->returnValue($oConfig));
        $oTheme->expects($this->at(4))->method('getInfo')->with($this->equalTo('id'))->will($this->returnValue('currentT'));
        $oTheme->activate();
    }

    public function testGetActiveThemeIdCustom()
    {
        $oConfig = $this->getMock('stdClass', array('getConfigParam'));
        $oConfig->expects($this->at(0))->method('getConfigParam')
            ->with(
                $this->equalTo('sCustomTheme')
            )
            ->will($this->returnValue('custom'));
        $oTheme = $this->getMock('oxTheme', array('getConfig'));
        $oTheme->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals('custom', $oTheme->getActiveThemeId());
    }

    public function testGetActiveThemeIdMain()
    {
        $oConfig = $this->getMock('stdClass', array('getConfigParam'));
        $oConfig->expects($this->at(0))->method('getConfigParam')
            ->with(
                $this->equalTo('sCustomTheme')
            )
            ->will($this->returnValue(''));
        $oConfig->expects($this->at(1))->method('getConfigParam')
            ->with(
                $this->equalTo('sTheme')
            )
            ->will($this->returnValue('maint'));
        $oTheme = $this->getMock('oxTheme', array('getConfig'));
        $oTheme->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals('maint', $oTheme->getActiveThemeId());
    }


    public function testGetParentNull()
    {
        $oTheme = $this->getMock('oxTheme', array('getInfo'));
        $oTheme->expects($this->any())->method('getInfo')->with($this->equalTo('parentTheme'))->will($this->returnValue(''));
        $this->assertNull($oTheme->getParent());
    }

    public function testGetParent()
    {
        $oTheme = $this->getMock('oxTheme', array('getInfo'));
        $oTheme->expects($this->any())->method('getInfo')->with($this->equalTo('parentTheme'))->will($this->returnValue('azure'));
        $oParent = $oTheme->getParent();
        $this->assertTrue($oParent instanceof \OxidEsales\EshopCommunity\Core\Theme);
        $this->assertEquals('azure', $oParent->getInfo('id'));
    }


    public function testCheckForActivationErrorsNoParent()
    {
        $oTheme = $this->getMock('oxTheme', array('getInfo'));
        $oTheme->expects($this->any())->method('getInfo')->with($this->equalTo('id'))->will($this->returnValue(''));
        $this->assertEquals('EXCEPTION_THEME_NOT_LOADED', $oTheme->checkForActivationErrors());


        $oTheme = $this->getMock('oxTheme', array('getInfo', 'getParent'));
        $oTheme->expects($this->at(0))->method('getInfo')->with($this->equalTo('id'))->will($this->returnValue('asd'));
        $oTheme->expects($this->at(1))->method('getParent')->will($this->returnValue(null));
        $oTheme->expects($this->at(2))->method('getInfo')->with($this->equalTo('parentTheme'))->will($this->returnValue('asd'));
        $this->assertEquals('EXCEPTION_PARENT_THEME_NOT_FOUND', $oTheme->checkForActivationErrors());


        $oTheme = $this->getMock('oxTheme', array('getInfo', 'getParent'));
        $oTheme->expects($this->at(0))->method('getInfo')->with($this->equalTo('id'))->will($this->returnValue('asd'));
        $oTheme->expects($this->at(1))->method('getParent')->will($this->returnValue(null));
        $oTheme->expects($this->at(2))->method('getInfo')->with($this->equalTo('parentTheme'))->will($this->returnValue(''));
        $this->assertFalse($oTheme->checkForActivationErrors());
    }

    public function testCheckForActivationErrorsCheckParent()
    {
        $oParent = $this->getMock('oxTheme', array('getInfo'));
        $oParent->expects($this->at(0))->method('getInfo')->with($this->equalTo('version'))->will($this->returnValue(''));

        $oTheme = $this->getMock('oxTheme', array('getInfo', 'getParent'));
        $oTheme->expects($this->at(0))->method('getInfo')->with($this->equalTo('id'))->will($this->returnValue('asd'));
        $oTheme->expects($this->at(1))->method('getParent')->will($this->returnValue($oParent));
        $this->assertEquals('EXCEPTION_PARENT_VERSION_UNSPECIFIED', $oTheme->checkForActivationErrors());

        ////
        $oParent = $this->getMock('oxTheme', array('getInfo'));
        $oParent->expects($this->at(0))->method('getInfo')->with($this->equalTo('version'))->will($this->returnValue('5'));

        $oTheme = $this->getMock('oxTheme', array('getInfo', 'getParent'));
        $oTheme->expects($this->at(0))->method('getInfo')->with($this->equalTo('id'))->will($this->returnValue('asd'));
        $oTheme->expects($this->at(1))->method('getParent')->will($this->returnValue($oParent));
        $oTheme->expects($this->at(2))->method('getInfo')->with($this->equalTo('parentVersions'))->will($this->returnValue(''));
        $this->assertEquals('EXCEPTION_UNSPECIFIED_PARENT_VERSIONS', $oTheme->checkForActivationErrors());

        ////
        $oParent = $this->getMock('oxTheme', array('getInfo'));
        $oParent->expects($this->at(0))->method('getInfo')->with($this->equalTo('version'))->will($this->returnValue('5'));

        $oTheme = $this->getMock('oxTheme', array('getInfo', 'getParent'));
        $oTheme->expects($this->at(0))->method('getInfo')->with($this->equalTo('id'))->will($this->returnValue('asd'));
        $oTheme->expects($this->at(1))->method('getParent')->will($this->returnValue($oParent));
        $oTheme->expects($this->at(2))->method('getInfo')->with($this->equalTo('parentVersions'))->will($this->returnValue(array(1, 2)));
        $this->assertEquals('EXCEPTION_PARENT_VERSION_MISMATCH', $oTheme->checkForActivationErrors());

        ////
        $oParent = $this->getMock('oxTheme', array('getInfo'));
        $oParent->expects($this->at(0))->method('getInfo')->with($this->equalTo('version'))->will($this->returnValue('5'));

        $oTheme = $this->getMock('oxTheme', array('getInfo', 'getParent'));
        $oTheme->expects($this->at(0))->method('getInfo')->with($this->equalTo('id'))->will($this->returnValue('asd'));
        $oTheme->expects($this->at(1))->method('getParent')->will($this->returnValue($oParent));
        $oTheme->expects($this->at(2))->method('getInfo')->with($this->equalTo('parentVersions'))->will($this->returnValue(array(1, 2, 5)));
        $this->assertFalse($oTheme->checkForActivationErrors());
    }

    public function testGetId()
    {
        $oTheme = oxNew('oxTheme');
        $oTheme->load("azure");

        $this->assertEquals('azure', $oTheme->getId());
    }

    /**
     * Test if getActiveThemeList gives correct list in simple case - one theme without extending
     */
    public function testGetActiveThemesListSimple()
    {
        $this->setConfigParam('sTheme', 'someTheme');

        $theme = oxNew('oxTheme');
        $this->assertEquals(['someTheme'], $theme->getActiveThemesList());
    }

    /**
     * Test if getActiveThemeList gives correct list if there are a theme which extends another theme
     */
    public function testGetActiveThemesListExtended()
    {
        $this->setConfigParam('sTheme', 'someBasicTheme');
        $this->setConfigParam('sCustomTheme', 'someImprovedTheme');

        $theme = oxNew('oxTheme');
        $this->assertEquals(['someBasicTheme', 'someImprovedTheme'], $theme->getActiveThemesList());
    }

    /**
     * Test if getActiveThemeList gives correct list if being in admin case
     */
    public function testGetActiveThemesListAdmin()
    {
        $this->setAdminMode(true);
        $this->setConfigParam('sTheme', 'someTheme');
        $this->setConfigParam('sCustomTheme', 'someCustomTheme');

        $theme = oxNew('oxTheme');
        $this->assertEquals(array(), $theme->getActiveThemesList());
    }
}
