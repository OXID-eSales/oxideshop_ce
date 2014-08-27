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
 * Class Unit_Core_oxOnlineLicenseCheckCallerTest
 *
 * @covers oxOnlineLicenseCheckCaller
 * @covers oxOnlineLicenseCheckRequest
 * @covers oxOnlineModulesNotifierRequest
 */
class Unit_Core_oxOnlineLicenseCheckCallerTest extends OxidTestCase
{

    public function testIfCorrectRequestPassedToXmlFormatter()
    {
        $oRequest = new oxOnlineLicenseCheckRequest();
        $oRequest->keys = new stdClass();
        $oRequest->keys->key = '_testKey';

        $oSimpleXml = $this->getMock('oxSimpleXml');
        $oSimpleXml->expects($this->atLeastOnce())->method('objectToXml')->with($oRequest, 'olcRequest');
        /** @var oxSimpleXml $oSimpleXml */

        $oCallerStub = $this->getMock('oxOnlineCaller', array('call'), array(), '', false);
        $oCallerStub->expects($this->once())->method('call')->will($this->returnValue($this->_getValidResponseXml()));
        /** @var oxOnlineCaller $oCallerStub */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCallerStub, $oSimpleXml);
        $oOnlineLicenseCaller->doRequest($oRequest);
    }

    public function testServiceCallWithCorrectRequest()
    {
        $sWebServiceUrl = 'https://olc.oxid-esales.com/check.php';

        $oSimpleXml = $this->getMock('oxSimpleXml');
        $oSimpleXml->expects($this->atLeastOnce())->method('objectToXml')->will($this->returnValue('formed_xml'));
        /** @var oxSimpleXml $oSimpleXml */

        $oCaller = $this->getMock('oxOnlineCaller', array('call'), array(), '', false);
        $oCaller->expects($this->once())->method('call')->with('formed_xml', $sWebServiceUrl)->will($this->returnValue($this->_getValidResponseXml()));
        /** @var oxOnlineCaller $oCaller */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCaller, $oSimpleXml);
        $oRequest = new oxOnlineLicenseCheckRequest();
        $oOnlineLicenseCaller->doRequest($oRequest);
    }

    public function testResponseFormedWithCorrectParameters()
    {
        $oSimpleXml = $this->getMock('oxSimpleXml');
        /** @var oxSimpleXml $oSimpleXml */

        $oCaller = $this->getMock('oxOnlineCaller', array('call'), array(), '', false);
        $oCaller->expects($this->once())->method('call')->will($this->returnValue($this->_getValidResponseXml()));
        /** @var oxOnlineCaller $oCaller */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCaller, $oSimpleXml);
        $oRequest = new oxOnlineLicenseCheckRequest();
        $oOnlineLicenseCaller->doRequest($oRequest);
    }

    public function providerUnexpectedExceptionIsThrownOnIncorrectResponse()
    {
        return array(
            array('OLC_ERROR_RESPONSE_NOT_VALID', ''),
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

        $oCaller = $this->getMock('oxOnlineCaller', array('call'), array(), '', false);
        $oCaller->expects($this->once())->method('call')->will($this->returnValue($sResponseXml));
        /** @var oxOnlineCaller $oCaller */

        $oSimpleXml = $this->getMock('oxSimpleXml');
        /** @var oxSimpleXml $oSimpleXml */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCaller, $oSimpleXml);
        $oRequest = new oxOnlineLicenseCheckRequest();
        $oOnlineLicenseCaller->doRequest($oRequest);
    }

    public function testCorrectResponseReturned()
    {
        $oExpectedResponse = new oxOnlineLicenseCheckResponse();
        $oExpectedResponse->code = 0;
        $oExpectedResponse->message = 'ACK';

        $oCaller = $this->getMock('oxOnlineCaller', array('call'), array(), '', false);
        $oCaller->expects($this->once())->method('call')->will($this->returnValue($this->_getValidResponseXml()));
        /** @var oxOnlineCaller $oCaller */

        $oSimpleXml = $this->getMock('oxSimpleXml');
        /** @var oxSimpleXml $oSimpleXml */

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCaller, $oSimpleXml);
        $oRequest = new oxOnlineLicenseCheckRequest();

        $this->assertEquals($oExpectedResponse, $oOnlineLicenseCaller->doRequest($oRequest));
    }

    /**
     * @return oxOnlineCaller
     */
    protected function _getValidResponseXml()
    {
        $sResponse = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
        $sResponse.= '<olc>';
        $sResponse.= '<code>0</code>';
        $sResponse.= '<message>ACK</message>';
        $sResponse.= '</olc>'.PHP_EOL;

        return $sResponse;
    }
}
