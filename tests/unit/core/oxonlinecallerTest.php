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
        $oCaller = new oxOnlineCaller($oCurl, $this->_getMockedEmailBuilder());
        $sUrl = 'http://oxid-esales.com';

        $this->assertSame('_testResult', $oCaller->call($sUrl, '_testXml'));
        $this->assertSame($sUrl, $oCurl->getUrl());
        $this->assertSame(array('xmlRequest' => '_testXml'), $oCurl->getParameters('xmlRequest'));
    }

    public function testCallWhenSucceedsFromFifthCall()
    {
        $oCaller = new oxOnlineCaller($this->_getMockedCurl(), $this->_getMockedEmailBuilder());
        $sUrl = 'http://oxid-esales.com';
        oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', 4);

        $this->assertSame('_testResult', $oCaller->call($sUrl, '_testXml'));
        $this->assertSame(0, oxRegistry::getConfig()->getConfigParam('iFailedOnlineCallsCount'));
    }

    public function testCallWhenFailsButItsFifthCall()
    {
        $oCaller = new oxOnlineCaller($this->_getMockedCurlWhichThrowsException(), $this->_getMockedEmailBuilder());
        $sUrl = 'http://oxid-esales.com';
        oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', 4);

        $this->assertNull($oCaller->call($sUrl, '_testXml'));
        $this->assertSame(5, oxRegistry::getConfig()->getConfigParam('iFailedOnlineCallsCount'));
    }

    public function testCallWhenFailsFromNotAllowedCallsCount()
    {
        $oEmail = $this->getMock('oxEmail', array('send'));
        // Email send function must be called.
        $oEmail->expects($this->once())->method('send')->will($this->returnValue(true));
        $oEmailBuilder = $this->getMock('oxOnlineServerEmailBuilder', array('build'));
        $oEmailBuilder->expects($this->any())->method('build')->will($this->returnValue($oEmail));

        $oCaller = new oxOnlineCaller($this->_getMockedCurlWhichThrowsException(), $oEmailBuilder);
        $sUrl = 'http://oxid-esales.com';
        oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', 5);

        $oCaller->call($sUrl, '_testXml');
        $this->assertSame(0, oxRegistry::getConfig()->getConfigParam('iFailedOnlineCallsCount'));
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

    /**
     * @return oxOnlineServerEmailBuilder
     */
    private function _getMockedEmailBuilder()
    {
        $oEmail = $this->getMock('oxEmail', array('send'));
        $oEmailBuilder = $this->getMock('oxOnlineServerEmailBuilder', array('build'));
        $oEmailBuilder->expects($this->any())->method('build')->will($this->returnValue($oEmail));

        return $oEmailBuilder;
    }
}
