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
 * Class Unit_Core_oxOnlineLicenseCheckCallerTest
 *
 * @covers oxOnlineLicenseCheckCaller
 * @covers oxOnlineCaller
 * @covers oxOnlineLicenseCheckRequest
 * @covers oxOnlineModulesNotifierRequest
 */
class Unit_Core_oxOnlineLicenseCheckCallerTest extends OxidTestCase
{

    public function testIfCorrectRequestPassedToXmlFormatter()
    {
        /** @var oxOnlineLicenseCheckRequest $oRequest */
        $oRequest = $this->getMock('oxOnlineLicenseCheckRequest', array(), array(), '', false);

        $oCurl = $this->getMock('oxCurl', array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($this->_getValidResponseXml()));
        /** @var oxCurl $oCurl */

        $oSimpleXml = $this->getMock('oxSimpleXml');
        $oSimpleXml->expects($this->atLeastOnce())->method('objectToXml')->with($oRequest, 'olcRequest');
        /** @var oxSimpleXml $oSimpleXml */

        /** @var oxOnlineServerEmailBuilder $oEmailBuilder */
        $oEmailBuilder = $this->getMock('oxOnlineServerEmailBuilder');

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, $oSimpleXml);
        $oOnlineLicenseCaller->doRequest($oRequest);
    }

    public function testServiceCallWithCorrectRequest()
    {
        $oSimpleXml = $this->getMock('oxSimpleXml');
        $oSimpleXml->expects($this->any())->method('objectToXml')->will($this->returnValue('formed_xml'));
        /** @var oxSimpleXml $oSimpleXml */

        /** @var oxOnlineServerEmailBuilder $oEmailBuilder */
        $oEmailBuilder = $this->getMock('oxOnlineServerEmailBuilder');

        $oCurl = $this->getMock('oxCurl', array('execute', 'setParameters'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($this->_getValidResponseXml()));
        $oCurl->expects($this->once())->method('setParameters')->with(array('xmlRequest' => 'formed_xml'));
        /** @var oxCurl $oCurl */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, $oSimpleXml);
        $oRequest = new oxOnlineLicenseCheckRequest();
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
        $this->setExpectedException('oxException', $sMessage);

        $oCurl = $this->getMock('oxCurl', array('execute', 'getStatusCode'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($sResponseXml));
        $oCurl->expects($this->any())->method('getStatusCode')->will($this->returnValue(200));
        /** @var oxCurl $oCurl */

        $oEmailBuilder = $this->getMock('oxOnlineServerEmailBuilder', array('build'));
        $oEmailBuilder->expects($this->any())->method('build');
        /** @var oxOnlineServerEmailBuilder $oEmailBuilder */

        $oSimpleXml = $this->getMock('oxSimpleXml');
        /** @var oxSimpleXml $oSimpleXml */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, $oSimpleXml);
        $oRequest = new oxOnlineLicenseCheckRequest();
        $oOnlineLicenseCaller->doRequest($oRequest);
    }

    public function testCorrectResponseReturned()
    {
        $oExpectedResponse = new oxOnlineLicenseCheckResponse();
        $oExpectedResponse->code = 0;
        $oExpectedResponse->message = 'ACK';

        $oCurl = $this->getMock('oxCurl', array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($this->_getValidResponseXml()));
        /** @var oxCurl $oCurl */

        $oSimpleXml = $this->getMock('oxSimpleXml');
        /** @var oxSimpleXml $oSimpleXml */

        $oEmailBuilder = $this->getMock('oxOnlineServerEmailBuilder', array('build'));
        $oEmailBuilder->expects($this->any())->method('build');
        /** @var oxOnlineServerEmailBuilder $oEmailBuilder */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, $oSimpleXml);
        $oRequest = new oxOnlineLicenseCheckRequest();

        $this->assertEquals($oExpectedResponse, $oOnlineLicenseCaller->doRequest($oRequest));
    }

    public function testCheckIfKeyWasRemovedWhenSendEmail()
    {
        $oCurl = $this->getMock('oxCurl', array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->throwException(new Exception()));
        /** @var oxCurl $oCurl */

        $oEmail = $this->getMock('oxEmail', array('send'));
        $oEmail->expects($this->any())->method('send');
        /** @var oxEmail $oEmail */

        $oEmailBuilder = $this->getMock('oxOnlineServerEmailBuilder', array('build'));
        $oEmailBuilder->expects($this->any())->method('build')->will($this->returnValue($oEmail));
        /** @var oxOnlineServerEmailBuilder $oEmailBuilder */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl, $oEmailBuilder, new oxSimpleXml());
        $oRequest = new oxOnlineLicenseCheckRequest();
        $oRequest->keys = '_testKeys';

        oxRegistry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 5);
        $this->setExpectedException('oxException', 'OLC_ERROR_RESPONSE_NOT_VALID');
        $oOnlineLicenseCaller->doRequest($oRequest);

        $this->assertEquals($oCurl->getParameters(), $oRequest->keys);
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
