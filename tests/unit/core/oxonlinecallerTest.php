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
 * Class Unit_Core_oxoOnlineCallerTest
 *
 * @covers oxOnlineCaller
 */
class Unit_Core_oxOnlineCallerTest extends OxidTestCase
{

    const SUT = 'oxOnlineCaller';

    public function testCallWhenSucceedsOnTheLastAllowedCall()
    {
        /** @var oxOnlineCaller $oCaller */
        $oCaller = $this->getMockForAbstractClass(
            'oxOnlineCaller',
            array($this->_getMockedCurl(), $this->_getMockedEmailBuilder(), $this->_getMockedSimpleXML()),
            '', true, true, true, array('_getXMLDocumentName', '_getServiceUrl')
        );
        oxRegistry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 4);
        $oCaller->call($this->_getRequest());

        $this->assertSame(0, oxRegistry::getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount'));
    }

    public function testCallWhenFailsAndItsLastAllowedCall()
    {
        /** @var oxOnlineCaller $oCaller */
        $oCaller = $this->getMockForAbstractClass(
            'oxOnlineCaller',
            array($this->_getMockedCurlWhichThrowsException(), $this->_getMockedEmailBuilder(), $this->_getMockedSimpleXML()),
            '', true, true, true, array('_getXMLDocumentName', '_getServiceUrl')
        );
        oxRegistry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 4);

        $this->assertNull($oCaller->call($this->_getRequest()));
        $this->assertSame(5, oxRegistry::getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount'));
    }

    public function testCallWhenFailsAndThereAreNotAllowedCallsCount()
    {
        $oEmail = $this->getMock('oxEmail', array('send'));
        // Email send function must be called.
        $oEmail->expects($this->once())->method('send')->will($this->returnValue(true));
        $oEmailBuilder = $this->getMock('oxOnlineServerEmailBuilder', array('build'));
        $oEmailBuilder->expects($this->any())->method('build')->will($this->returnValue($oEmail));

        $oCaller = $this->getMockForAbstractClass(
            'oxOnlineCaller',
            array($this->_getMockedCurlWhichThrowsException(), $oEmailBuilder, $this->_getMockedSimpleXML()),
            '', true, true, true, array('_getXMLDocumentName', '_getServiceUrl')
        );
        $oCaller->expects($this->any())->method('_getXMLDocumentName')->will($this->returnValue('testXML'));
        /** @var oxOnlineCaller $oCaller */
        oxRegistry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 5);

        $oCaller->call($this->_getRequest());
        $this->assertSame(0, oxRegistry::getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount'));
    }

    public function testCallWhenStatusCodeIndicatesError()
    {
        $oCurl = $this->getMock('oxCurl', array('execute', 'getStatusCode'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue('_testResult'));
        $oCurl->expects($this->any())->method('getStatusCode')->will($this->returnValue(500));

        /** @var oxOnlineCaller $oCaller */
        $oCaller = $this->getMockForAbstractClass(
            'oxOnlineCaller',
            array($oCurl, $this->_getMockedEmailBuilder(), $this->_getMockedSimpleXML()),
            '', true, true, true, array('_getXMLDocumentName', '_getServiceUrl')
        );
        oxRegistry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 4);
        $oCaller->call($this->_getRequest());

        $this->assertSame(5, oxRegistry::getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount'));
    }

    /**
     * Test if timeout option was set before calling the curl execute method.
     */
    public function testCallSetsTimeoutOptionForCurlExecution()
    {
        // Arrange
        $curlDouble = new oxOnlineCallerOxCurlOptionDouble();

        /** @var oxOnlineCaller $sut */
        $sut = $this->getMockBuilder(static::SUT)
            ->setConstructorArgs(
                array(
                    $curlDouble,
                    $this->_getMockedEmailBuilder(),
                    $this->_getMockedSimpleXML()
                )
            )
            ->getMockForAbstractClass();

        // Act
        $sut->call($this->_getRequest());

        // Assert
        $expectedOptionValue = oxOnlineCaller::CURL_EXECUTION_TIMEOUT;
        $actualOptionValue = $curlDouble->getOption(
            oxCurl::EXECUTION_TIMEOUT_OPTION
        );

        $this->assertSame($expectedOptionValue, $actualOptionValue);
    }

    /**
     * @return oxCurl
     */
    private function _getMockedCurl()
    {
        $oCurl = $this->getMock('oxCurl', array('execute', 'getStatusCode'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue('_testResult'));
        $oCurl->expects($this->any())->method('getStatusCode')->will($this->returnValue(200));

        /** @var oxCurl $oCurl */

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

    /**
     * @return oxOnlineRequest
     */
    private function _getRequest()
    {
        $oRequest = new oxOnlineRequest();
        $oRequest->clusterId = '_testClusterId';
        $oRequest->edition = '_testEdition';
        $oRequest->version = '_testVersion';
        $oRequest->shopUrl = '_testUrl';

        return $oRequest;
    }

    /**
     * @return string
     */
    private function _getResponseXML()
    {
        $sResultXML = '<?xml version="1.0" encoding="utf-8"?>
<onlineRequest><clusterId>_testClusterId</clusterId><edition>_testEdition</edition><version>_testVersion</version><shopUrl>_testUrl</shopUrl><pVersion/><productId>eShop</productId></onlineRequest>
';

        return $sResultXML;
    }

    /**
     * @return oxSimpleXml
     */
    private function _getMockedSimpleXML()
    {
        $oSimpleXML = $this->getMock('oxSimpleXml', array('objectToXml'));
        $oSimpleXML->expects($this->any())->method('objectToXml')->will($this->returnValue('_someXML'));

        return $oSimpleXML;
    }
}

/**
 * Class oxOnlineCallerOxCurlOptionDouble
 *
 * This is a test double for oxCurl class.
 *
 * This class is used to check if a given option was set before calling the
 * execute method. In order to make an assertion of the fact, just check the
 * value of getOption method.
 */
class oxOnlineCallerOxCurlOptionDouble extends oxCurl
{
    /** @var array Hash map of options which were set before execution */
    private $options;

    /** @var bool Flag which indicated that execute method was called */
    private $executionCalled;

    public function __construct()
    {
        $this->options = array();
        $this->executionCalled = false;
    }

    public function setOption($name, $value)
    {
        if (!$this->executionCalled) {
            $this->options[$name] = $value;
        }
    }

    public function getOption($name)
    {
        $result = null;

        $callCondition = $this->executionCalled;
        $keyExistsCondition = array_key_exists($name, $this->options);

        if ($callCondition && $keyExistsCondition) {
            $result = $this->options[$name];
        }

        return $result;
    }

    public function execute()
    {
        $this->executionCalled = true;
    }
}
