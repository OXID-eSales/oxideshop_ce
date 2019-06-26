<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use \oxOnlineLicenseCheckCaller;
use \Exception;
use \oxSimpleXml;
use \oxTestModules;

/**
 * Class Unit_Core_oxOnlineLicenseCheckCallerTest
 *
 * @covers oxOnlineLicenseCheckCaller
 * @covers oxOnlineCaller
 * @covers oxOnlineLicenseCheckRequest
 * @covers oxOnlineModulesNotifierRequest
 */
class OnlineLicenseCheckCallerTest extends \OxidTestCase
{
    public function testIfCorrectRequestPassedToXmlFormatter()
    {
        /** @var oxOnlineLicenseCheckRequest $oRequest */
        $oRequest = $this->getMock(\OxidEsales\Eshop\Core\OnlineLicenseCheckRequest::class, array(), array(), '', false);

        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($this->_getValidResponseXml()));
        /** @var oxCurl $oCurl */

        $oSimpleXml = $this->getMock('oxSimpleXml');
        $oSimpleXml->expects($this->atLeastOnce())->method('objectToXml')->with($oRequest, 'olcRequest');
        /** @var oxSimpleXml $oSimpleXml */

        /** @var OnlineServerEmailBuilder $oEmailBuilder */
        $oEmailBuilder = $this->getMock(OnlineServerEmailBuilder::class);

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, $oSimpleXml);
        $oOnlineLicenseCaller->doRequest($oRequest);
    }

    public function testServiceCallWithCorrectRequest()
    {
        $oSimpleXml = $this->getMock('oxSimpleXml');
        $oSimpleXml->expects($this->any())->method('objectToXml')->will($this->returnValue('formed_xml'));
        /** @var oxSimpleXml $oSimpleXml */

        /** @var OnlineServerEmailBuilder $oEmailBuilder */
        $oEmailBuilder = $this->getMock(OnlineServerEmailBuilder::class);

        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute', 'setParameters'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($this->_getValidResponseXml()));
        $oCurl->expects($this->once())->method('setParameters')->with(array('xmlRequest' => 'formed_xml'));
        /** @var oxCurl $oCurl */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, $oSimpleXml);
        $oRequest = oxNew('oxOnlineLicenseCheckRequest');
        $oOnlineLicenseCaller->doRequest($oRequest);
    }

    public function providerUnexpectedExceptionIsThrownOnIncorrectResponse()
    {
        return array(
            array('OLC_ERROR_RESPONSE_NOT_VALID', ''),
            array('OLC_ERROR_RESPONSE_NOT_VALID', 'any random non xml text'),
            array('OLC_ERROR_RESPONSE_NOT_VALID', '<?xml version="1.0" encoding="utf-8"?>',),
            array('OLC_ERROR_RESPONSE_UNEXPECTED', '<?xml version="1.0" encoding="utf-8"?><test></test>'),
            array('OLC_ERROR_RESPONSE_UNEXPECTED', '<?xml version="1.0" encoding="utf-8"?><invalid_license><code>123</code></invalid_license>'),
            array('OLC_ERROR_RESPONSE_NOT_VALID', '<?xml version="1.0" encoding="utf-8"?><olc></olc>'),
            array('OLC_ERROR_RESPONSE_NOT_VALID', '<?xml version="1.0" encoding="utf-8"?><olc></olc>'),
        );
    }

    /**
     * Test get response information from parsed response message.
     *
     * @dataProvider providerUnexpectedExceptionIsThrownOnIncorrectResponse
     */
    public function testUnexpectedExceptionIsThrownOnIncorrectResponse($sMessage, $sResponseXml)
    {
        $this->expectException('oxException');
        $this->expectExceptionMessage($sMessage);

        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute', 'getStatusCode'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($sResponseXml));
        $oCurl->expects($this->any())->method('getStatusCode')->will($this->returnValue(200));
        /** @var oxCurl $oCurl */

        $oEmailBuilder = $this->getMock(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class, array('build'));
        $oEmailBuilder->expects($this->any())->method('build');
        /** @var OnlineServerEmailBuilder $oEmailBuilder */

        $oSimpleXml = $this->getMock('oxSimpleXml');
        /** @var oxSimpleXml $oSimpleXml */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, $oSimpleXml);
        $oRequest = oxNew('oxOnlineLicenseCheckRequest');
        $oOnlineLicenseCaller->doRequest($oRequest);
    }

    public function testCorrectResponseReturned()
    {
        $oExpectedResponse = oxNew('oxOnlineLicenseCheckResponse');
        $oExpectedResponse->code = 0;
        $oExpectedResponse->message = 'ACK';

        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($this->_getValidResponseXml()));
        /** @var oxCurl $oCurl */

        $oSimpleXml = $this->getMock('oxSimpleXml');
        /** @var oxSimpleXml $oSimpleXml */

        $oEmailBuilder = $this->getMock(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class, array('build'));
        $oEmailBuilder->expects($this->any())->method('build');
        /** @var OnlineServerEmailBuilder $oEmailBuilder */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, $oSimpleXml);
        $oRequest = oxNew('oxOnlineLicenseCheckRequest');

        $this->assertEquals($oExpectedResponse, $oOnlineLicenseCaller->doRequest($oRequest));
    }

    public function testLicenseKeysWereRemovedFromEmailBody()
    {
        /** A normal request would among other things contain the license keys  */
        $oRequest = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckRequest::class);
        $oRequest->keys = ['key' => ['license_key_1', 'license_key_2',]];

        /**
         * The business logic sets the removes the license keys in the request, which is passed to the email builder.
         * The request is converted to a XML string and passed to the email builder as  email body.
         */
        $emailRequest = clone $oRequest;
        $emailRequest->keys = null;
        $simpleXml = new \OxidEsales\Eshop\Core\SimpleXml();
        $expectedEmailBody = $simpleXml->objectToXml($emailRequest, 'olcRequest');

        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute'));
        $stubbedExceptionMessage = 'This is a stubbed exception';
        $oCurl->expects($this->any())->method('execute')->will($this->throwException(new Exception($stubbedExceptionMessage)));

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('send'));
        $oEmail->expects($this->any())->method('send');

        $oEmailBuilder = $this->getMock(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class, array('build'));
        $oEmailBuilder->expects($this->any())
            ->method('build')
            ->with($expectedEmailBody)
            ->will($this->returnValue($oEmail));

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, $simpleXml);

        $this->getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 5);
        try {
            $oOnlineLicenseCaller->doRequest($oRequest);
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            $this->assertSame('OLC_ERROR_RESPONSE_NOT_VALID', $exception->getMessage());
        }

        /**
         * The business logic will log the to the exception log, whenever an email is sent, but will not re-throw the error
         */
        $expectedExceptionClass = \OxidEsales\Eshop\Core\Exception\StandardException::class;
        $this->assertLoggedException($expectedExceptionClass, $stubbedExceptionMessage);
    }

    /**
     * @return oxOnlineCaller
     */
    protected function _getValidResponseXml()
    {
        $sResponse = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL;
        $sResponse .= '<olc>';
        $sResponse .= '<code>0</code>';
        $sResponse .= '<message>ACK</message>';
        $sResponse .= '</olc>' . PHP_EOL;

        return $sResponse;
    }
}
