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

/**
 * Class Unit_Core_oxoOnlineCallerTest
 *
 * @covers oxOnlineCaller
 */
class Unit_Core_oxoOnlineCallerTest extends OxidTestCase
{
    public function testCallWhenSucceedsAndAllValuesWereSet()
    {
        $oCurl = $this->_getMockedCurl();
        $oCaller = new oxOnlineCaller($oCurl);
        $sUrl = 'http://oxid-esales.com';
        $aCurlParameters = array('xmlRequest' => '_testXml');

        $this->assertSame('_testResult', $oCaller->call($sUrl, $aCurlParameters));
        $this->assertSame($sUrl, $oCurl->getUrl());
        $this->assertSame(array('xmlRequest' => '_testXml'), $oCurl->getParameters('xmlRequest'));
    }

    public function testCallWhenSucceedsFromFifthCall()
    {
        $oCaller = new oxOnlineCaller($this->_getMockedCurl());
        $sUrl = 'http://oxid-esales.com';
        $aCurlParameters = array('xmlRequest' => '_testXml');
        oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', 4);

        $this->assertSame('_testResult', $oCaller->call($sUrl, $aCurlParameters));
        $this->assertSame(0, oxRegistry::getConfig()->getConfigParam('iFailedOnlineCallsCount'));
    }

    public function testCallWhenFailsButItsFifthCall()
    {
        $oCaller = new oxOnlineCaller($this->_getMockedCurlWhichThrowsException());
        $sUrl = 'http://oxid-esales.com';
        $aCurlParameters = array('xmlRequest' => '_testXml');
        oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', 4);

        $this->assertNull($oCaller->call($sUrl, $aCurlParameters));
        $this->assertSame(5, oxRegistry::getConfig()->getConfigParam('iFailedOnlineCallsCount'));
    }

    public function testCallWhenFailsFromSixthCall()
    {
        $oCaller = new oxOnlineCaller($this->_getMockedCurlWhichThrowsException());
        $sUrl = 'http://oxid-esales.com';
        $aCurlParameters = array('xmlRequest' => '_testXml');
        oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', 5);

        $this->setExpectedException('oxException', oxRegistry::getLang()->translateString( 'OLC_ERROR_REQUEST_FAILED' ));
        $oCaller->call($sUrl, $aCurlParameters);
    }

    /**
     * @return oxCurl
     */
    private function _getMockedCurl()
    {
        /** @var oxCurl $oCurl */
        $oCurl = $this->getMock('oxCurl', array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue('_testResult'));

        return $oCurl;
    }

    /**
     * @return oxCurl
     */
    private function _getMockedCurlWhichThrowsException()
    {
        $oCurl = $this->getMock('oxCurl', array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->throwException(new Exception()));

        return $oCurl;
    }
}
