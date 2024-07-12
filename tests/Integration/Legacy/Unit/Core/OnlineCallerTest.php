<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxCurl;
use \Exception;
use \oxOnlineCaller;
use \oxOnlineRequest;
use \oxTestModules;
use \OxidEsales\Eshop\Core\OnlineServerEmailBuilder;

/**
 * Class Unit_Core_oxoOnlineCallerTest
 *
 * @covers oxOnlineCaller
 */
class OnlineCallerTest extends \OxidTestCase
{
    public const SUT = 'oxOnlineCaller';

    public function testCallWhenSucceedsOnTheLastAllowedCall()
    {
        /** @var oxOnlineCaller $oCaller */
        $oCaller = $this->getMockForAbstractClass(
            'oxOnlineCaller',
            [$this->getMockedCurl(), $this->getMockedEmailBuilder(), $this->getMockedSimpleXML()],
            '',
            true,
            true,
            true,
            ['getXMLDocumentName', 'getServiceUrl']
        );
        $this->getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 4);
        $oCaller->call($this->getRequest());

        $this->assertSame(0, $this->getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount'));
    }

    public function testCallWhenFailsAndItsLastAllowedCall()
    {
        /** @var oxOnlineCaller $oCaller */
        $oCaller = $this->getMockForAbstractClass(
            'oxOnlineCaller',
            [$this->getMockedCurlWhichThrowsException(), $this->getMockedEmailBuilder(), $this->getMockedSimpleXML()],
            '',
            true,
            true,
            true,
            ['getXMLDocumentName', 'getServiceUrl']
        );
        $this->getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 4);

        $this->assertNull($oCaller->call($this->getRequest()));
        $this->assertSame(5, $this->getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount'));
    }

    public function testCallWhenFailsAndThereAreNotAllowedCallsCount()
    {
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['send']);
        // Email send function must be called.
        $oEmail->expects($this->once())->method('send')->will($this->returnValue(true));
        $oEmailBuilder = $this->getMock(OnlineServerEmailBuilder::class, ['build']);
        $oEmailBuilder->expects($this->any())->method('build')->will($this->returnValue($oEmail));

        $oCaller = $this->getMockForAbstractClass(
            'oxOnlineCaller',
            [$this->getMockedCurlWhichThrowsException(), $oEmailBuilder, $this->getMockedSimpleXML()],
            '',
            true,
            true,
            true,
            ['getXMLDocumentName', 'getServiceUrl']
        );
        $oCaller->expects($this->any())->method('getXMLDocumentName')->will($this->returnValue('testXML'));
        $this->getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 5);

        $oCaller->call($this->getRequest());
        $this->assertSame(0, $this->getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount'));

        /**
         * Although no exception is thrown, the underlying error will be logged in oxideshop.log
         */
        $expectedExceptionClass = Exception::class;
        $this->assertLoggedException($expectedExceptionClass);
    }

    public function testCallWhenStatusCodeIndicatesError()
    {
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ['execute', 'getStatusCode']);
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue('testResult'));
        $oCurl->expects($this->any())->method('getStatusCode')->will($this->returnValue(500));

        /** @var oxOnlineCaller $oCaller */
        $oCaller = $this->getMockForAbstractClass(
            'oxOnlineCaller',
            [$oCurl, $this->getMockedEmailBuilder(), $this->getMockedSimpleXML()],
            '',
            true,
            true,
            true,
            ['getXMLDocumentName', 'getServiceUrl']
        );
        $this->getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 4);
        $oCaller->call($this->getRequest());

        $this->assertSame(5, $this->getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount'));
        $this->getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 0);
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
                [$curlDouble, $this->getMockedEmailBuilder(), $this->getMockedSimpleXML()]
            )
            ->getMockForAbstractClass();

        // Act
        $sut->call($this->getRequest());

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
    private function getMockedCurl()
    {
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ['execute', 'getStatusCode']);
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue('testResult'));
        $oCurl->expects($this->any())->method('getStatusCode')->will($this->returnValue(200));

        /** @var oxCurl $oCurl */

        return $oCurl;
    }

    /**
     * @return oxCurl
     */
    private function getMockedCurlWhichThrowsException()
    {
        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, ['execute']);
        $oCurl->expects($this->any())->method('execute')->will($this->throwException(new Exception()));

        return $oCurl;
    }

    /**
     * @return OnlineServerEmailBuilder
     */
    private function getMockedEmailBuilder()
    {
        $emailMock = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ['send']);

        $emailBuilderMock = $this->getMock(OnlineServerEmailBuilder::class, ['build']);
        $emailBuilderMock->expects($this->any())->method('build')->will($this->returnValue($emailMock));

        /** @var OnlineServerEmailBuilder $emailBuilderMock */
        return $emailBuilderMock;
    }

    /**
     * @return oxOnlineRequest
     */
    private function getRequest()
    {
        $oRequest = oxNew('oxOnlineRequest');
        $oRequest->clusterId = '_testClusterId';
        $oRequest->edition = '_testEdition';
        $oRequest->version = '_testVersion';
        $oRequest->shopUrl = '_testUrl';

        return $oRequest;
    }

    /**
     * @return oxSimpleXml
     */
    private function getMockedSimpleXML()
    {
        $oSimpleXML = $this->getMock(\OxidEsales\Eshop\Core\SimpleXml::class, ['objectToXml']);
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
    private $options = [];

    /** @var bool Flag which indicated that execute method was called */
    private $executionCalled = false;

    public function __construct()
    {
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
            return $this->options[$name];
        }

        return $result;
    }

    public function execute()
    {
        $this->executionCalled = true;
    }
}
