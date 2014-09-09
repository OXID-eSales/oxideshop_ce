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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath(".") . '/unit/OxidTestCase.php';
require_once realpath(".") . '/unit/test_config.inc.php';

/**
 * Testing oxstart class
 */
class Unit_Views_oxstartTest extends OxidTestCase
{

    public function testRenderNormal()
    {
        $oStart = new oxStart();
        $oStart->getConfig();
        $sRes = $oStart->render();
        $this->assertEquals('message/err_unknown.tpl', $sRes);
    }



    public function testGetErrorNumber()
    {
        $oStart = $this->getProxyClass('oxstart');
        $this->setRequestParam('errornr', 123);

        $this->assertEquals(123, $oStart->getErrorNumber());
    }


    public function testPageCloseNoSess()
    {
        $oStart = $this->getMock('oxstart', array('getSession'));
        $oStart->expects($this->once())->method('getSession')->will($this->returnValue(null));

        $oUtils = $this->getMock('oxUtils', array('commitFileCache'));
        $oUtils->expects($this->once())->method('commitFileCache')->will($this->returnValue(null));

        oxTestModules::addModuleObject('oxUtils', $oUtils);
        $this->assertEquals(null, $oStart->pageClose());
    }


    public function testPageClose()
    {
        $oSess = $this->getMock('stdclass', array('freeze'));
        $oSess->expects($this->once())->method('freeze')->will($this->returnValue(null));

        $oStart = $this->getMock('oxstart', array('getSession'));
        $oStart->expects($this->once())->method('getSession')->will($this->returnValue($oSess));

        $oUtils = $this->getMock('oxUtils', array('commitFileCache'));
        $oUtils->expects($this->once())->method('commitFileCache')->will($this->returnValue(null));

        oxTestModules::addModuleObject('oxUtils', $oUtils);
        $this->assertEquals(null, $oStart->pageClose());
    }

    public function testProcessingOfServerNodes()
    {
        /** @var oxServerProcessor $oProcessor */
        $oProcessor = $this->getMock('oxServerProcessor');
        $oProcessor->expects($this->once())->method('process')->will($this->returnValue(null));

        $oStart = $this->getMock('oxStart', array('_getServerProcessor', 'pageStart'));
        $oStart->expects($this->any())->method('_getServerProcessor')->will($this->returnValue($oProcessor));
        $oStart->expects($this->any())->method('pageStart')->will($this->returnValue(null));

        $oStart->appInit();
    }

    public function testAppInitOnShopStartEventCalled()
    {
        $oSystemEventHandler = $this->getMock('oxSystemEventHandler');
        $oSystemEventHandler->expects($this->once())->method('onShopStart')->will($this->returnValue(null));

        $oServerProcessor = $this->getMock('oxServerProcessor');
        $oServerProcessor->expects($this->any())->method('process')->will($this->returnValue(null));

        $oStart = $this->getMock('oxStart', array('_getSystemEventHandler', '_getServerProcessor', '_needValidateShop'));
        $oStart->expects($this->any())->method('_getSystemEventHandler')->will($this->returnValue($oSystemEventHandler));
        $oStart->expects($this->any())->method('_getServerProcessor')->will($this->returnValue($oServerProcessor));
        $oStart->expects($this->any())->method('_needValidateShop')->will($this->returnValue(false));

        $oStart->appInit();
    }
}