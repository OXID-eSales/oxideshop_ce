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
    public function providerDoRequestWithAllSetXmlValues()
    {
        $sOnlineLicenseCheckXml = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
        $sOnlineLicenseCheckXml.= '<olcRequest>';
        $sOnlineLicenseCheckXml.= '<keys><key>_testKey</key></keys>';
        $sOnlineLicenseCheckXml.= '<revision>_testRevision</revision>';
        $sOnlineLicenseCheckXml.= '<edition>_testEdition</edition>';
        $sOnlineLicenseCheckXml.= '<version>_testVersion</version>';
        $sOnlineLicenseCheckXml.= '<shopurl>_testShopUrl</shopurl>';
        $sOnlineLicenseCheckXml.= '<pversion>_testPVersion</pversion>';
        $sOnlineLicenseCheckXml.= '<productid>_testProductId</productid>';
        $sOnlineLicenseCheckXml.= '</olcRequest>'.PHP_EOL;

        $sModuleNotifierXml = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
        $sModuleNotifierXml.= '<olcRequest>';
        $sModuleNotifierXml.= '<modules>_testModule</modules>';
        $sModuleNotifierXml.= '<edition>_testEdition</edition>';
        $sModuleNotifierXml.= '<version>_testVersion</version>';
        $sModuleNotifierXml.= '<shopurl>_testShopUrl</shopurl>';
        $sModuleNotifierXml.= '<pversion>_testPVersion</pversion>';
        $sModuleNotifierXml.= '<productid>_testProductId</productid>';
        $sModuleNotifierXml.= '</olcRequest>'.PHP_EOL;


        return array(
            array('OLC', 'https://olc.oxid-esales.com/check.php', $sOnlineLicenseCheckXml),
            array('OMVN', 'https://omvn.oxid-esales.com/check.php', $sModuleNotifierXml)
        );
    }

    /**
     * @param string $sWebServiceUrlType
     * @param string $sWebServiceUrl
     * @param string $sXml
     *
     * @dataProvider providerDoRequestWithAllSetXmlValues
     */
    public function testDoRequestWithAllSetXmlValues($sWebServiceUrlType, $sWebServiceUrl, $sXml)
    {
        /** @var oxCurl $oCurl */
        $oCurl = $this->getMock('oxCurl', array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue(true));

        $oOnlineLicenseCheckRequest = $this->_getRequestByServiceType($sWebServiceUrlType);
        if ($sWebServiceUrlType == 'OLC') {
            $oOnlineLicenseCheckRequest->keys = new stdClass();
            $oOnlineLicenseCheckRequest->keys->key = '_testKey';
            $oOnlineLicenseCheckRequest->revision = '_testRevision';
        } else {
            $oOnlineLicenseCheckRequest->modules = '_testModule';
        }
        $oOnlineLicenseCheckRequest->version = '_testVersion';
        $oOnlineLicenseCheckRequest->edition = '_testEdition';
        $oOnlineLicenseCheckRequest->shopurl = '_testShopUrl';
        $oOnlineLicenseCheckRequest->pversion = '_testPVersion';
        $oOnlineLicenseCheckRequest->productid = '_testProductId';

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl);
        $oOnlineLicenseCaller->doRequest($oOnlineLicenseCheckRequest, $sWebServiceUrlType);

        $this->assertSame($sWebServiceUrl, $oCurl->getUrl());
        $this->assertSame('POST', $oCurl->getMethod());
        $this->assertEquals(array('xmlRequest' => $sXml), $oCurl->getParameters());
    }

    public function providerDoRequestWithKeys()
    {
        return array(
            // When keys are in array.
            array(array('first key', 'second key')),
            // When one key is given.
            array('one key')
        );
    }

    /**
     * @param $mxKeys
     *
     * @dataProvider providerDoRequestWithKeys
     */
    public function testDoRequestWithKeys($mxKeys)
    {
        /** @var oxCurl $oCurl */
        $oCurl = $this->getMock('oxCurl', array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue(true));

        $oOnlineLicenseCheckRequest = $this->_getRequestByServiceType('OLC');
        $oOnlineLicenseCheckRequest->keys = new stdClass();
        $oOnlineLicenseCheckRequest->keys->key = $mxKeys;

        //expected xml file source
        $sXml = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
        $sXml.= '<olcRequest>';
        if (is_array($mxKeys)) {
            $sXml.= '<keys><key>first key</key><key>second key</key></keys>';
        } else {
            $sXml.= '<keys><key>one key</key></keys>';
        }
        $sXml.= '<revision/><edition/><version/><shopurl/><pversion/><productid/>';
        $sXml.= '</olcRequest>'.PHP_EOL;

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl);
        $oOnlineLicenseCaller->doRequest($oOnlineLicenseCheckRequest, 'OLC');

        $this->assertEquals(array('xmlRequest' => $sXml), $oCurl->getParameters());
    }

    public function testDoRequestWhenExceptionIsThrown()
    {
        $oCurl = $this->getMock('oxCurl', array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->throwException(new Exception()));
        $oOnlineLicenseCheckRequest = new oxOnlineModulesNotifierRequest();
        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller($oCurl);

        $this->setExpectedException('oxException', oxRegistry::getLang()->translateString('OMVN_ERROR_REQUEST_FAILED'));
        $oOnlineLicenseCaller->doRequest($oOnlineLicenseCheckRequest, 'OMVN');
    }

    /**
     * Test get response information from parsed response message.
     */
    public function testGetParsedResponseMessage()
    {
        $sExpectedResponseStatusCode = '123';
        $sExpectedResponseStatusMessage = 'testmessage';
        $sRawResponseMessage = '<?xml version="1.0" encoding="utf-8"?>';
        $sRawResponseMessage .= '<olc>';
        $sRawResponseMessage .= '<code>' . $sExpectedResponseStatusCode . '</code>';
        $sRawResponseMessage .= '<message>' . $sExpectedResponseStatusMessage . '</message>';
        $sRawResponseMessage .= '</olc>';

        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller();
        $oOnlineLicenseCaller->setRawResponseMessage($sRawResponseMessage);

        $oResponse = $oOnlineLicenseCaller->_formResponse();

        $this->assertEquals( $sExpectedResponseStatusCode, $oResponse->code );
        $this->assertEquals( $sExpectedResponseStatusMessage, $oResponse->message );
    }

    /**
     * Test parse response with exception when response parameters are provided.
     */
    public function testGetResponseExceptionNoParameters()
    {
        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller();

        $sRawResponseMessage = '<?xml version="1.0" encoding="utf-8"?>';
        $sRawResponseMessage .= '<olc>';
        $sRawResponseMessage .= '</olc>';

        $oOnlineLicenseCaller->setRawResponseMessage( $sRawResponseMessage );

        $this->setExpectedException( 'oxException', oxRegistry::getLang()->translateString( 'OLC_ERROR_RESPONSE_NOT_VALID' ) );
        $oOnlineLicenseCaller->_formResponse();
    }

    /**
     * Test parse response with exception when unable to load xml.
     */
    public function testGetResponseExceptionWhenUnableToLoad()
    {
        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller();

        $sRawResponseMessage = '<?xml versio';
        $sRawResponseMessage .= '<olc>';
        $sRawResponseMessage .= '</olc>';

        $oOnlineLicenseCaller->setRawResponseMessage( $sRawResponseMessage );

        $this->setExpectedException( 'oxException', oxRegistry::getLang()->translateString( 'OLC_ERROR_RESPONSE_NOT_VALID' ) );
        $oOnlineLicenseCaller->_formResponse();
    }

    /**
     * Test parse response with exception when response is unexpected.
     */
    public function testGetResponseExceptionResponseUnexpected()
    {
        $oOnlineLicenseCaller = new oxOnlineLicenseCheckCaller();

        $sRawResponseMessage = '<?xml version="1.0" encoding="utf-8"?>';
        $sRawResponseMessage .= '<anything>';
        $sRawResponseMessage .= '</anything>';

        $oOnlineLicenseCaller->setRawResponseMessage( $sRawResponseMessage );

        $this->setExpectedException( 'oxException', oxRegistry::getLang()->translateString( 'OLC_ERROR_RESPONSE_UNEXPECTED' ) );
        $oOnlineLicenseCaller->_formResponse();
    }

    /**
     * @param $sServiceType
     *
     * @return oxOnlineLicenseCheckRequest|oxOnlineModulesNotifierRequest
     */
    private function _getRequestByServiceType($sServiceType)
    {
        if ($sServiceType === 'OLC') {
            $oRequest = new oxOnlineLicenseCheckRequest();
        } else {
            $oRequest = new oxOnlineModulesNotifierRequest();
        }

        return $oRequest;
    }
}
