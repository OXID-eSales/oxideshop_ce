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

class Unit_Core_oxOnlineLicenseCallerTest extends OxidTestCase
{
    public function testDoRequest()
    {
        /** @var oxCurl $oCurl */
        $oCurl = $this->getMock('oxCurl', array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue(true));

        $oOnlineLicenseCheckRequest = new oxOnlineLicenseCheckRequest();
        $oOnlineLicenseCheckRequest->edition = "edition";
        $oOnlineLicenseCheckRequest->version = "version";
        $oOnlineLicenseCheckRequest->revision = "revision";
        $oOnlineLicenseCheckRequest->shopurl = "shopUrl";
        $oOnlineLicenseCheckRequest->pversion = "pVersion";
        $oOnlineLicenseCheckRequest->productid = "productId";

        $oOnlineLicenseCaller = new oxOnlineLicenseCaller($oCurl);

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

        $oOnlineLicenseCaller = new oxOnlineLicenseCaller();
        $oOnlineLicenseCaller->setRawResponseMessage($sRawResponseMessage);

        $oResponse = $oOnlineLicenseCaller->getParsedResponseMessage();

        $this->assertEquals( $sExpectedResponseStatusCode, $oResponse->code );
        $this->assertEquals( $sExpectedResponseStatusMessage, $oResponse->message );
    }

    /**
     * Test parse response with exception when response parameters are provided.
     */
    public function testGetResponseExceptionNoParameters()
    {
        $oOnlineLicenseCaller = new oxOnlineLicenseCaller();

        $sRawResponseMessage = '<?xml version="1.0" encoding="utf-8"?>';
        $sRawResponseMessage .= '<olc>';
        $sRawResponseMessage .= '</olc>';

        $oOnlineLicenseCaller->setRawResponseMessage( $sRawResponseMessage );

        $this->setExpectedException( 'oxException', oxRegistry::getLang()->translateString( 'OLC_ERROR_RESPONSE_NOT_VALID' ) );
        $oOnlineLicenseCaller->getParsedResponseMessage();
    }

    /**
     * Test parse response with exception when response is unexpected.
     */
    public function testGetResponseExceptionResponseUnexpected()
    {
        $oOnlineLicenseCaller = new oxOnlineLicenseCaller();

        $sRawResponseMessage = '<?xml version="1.0" encoding="utf-8"?>';
        $sRawResponseMessage .= '<anything>';
        $sRawResponseMessage .= '</anything>';

        $oOnlineLicenseCaller->setRawResponseMessage( $sRawResponseMessage );

        $this->setExpectedException( 'oxException', oxRegistry::getLang()->translateString( 'OLC_ERROR_RESPONSE_UNEXPECTED' ) );
        $oOnlineLicenseCaller->getParsedResponseMessage();
    }

    public function testGenerateXml_whenPassingTwoKeysThroughParameters()
    {
        $aKeys[] = 'first key';
        $aKeys[] = 'second key';

        $oOnlineLicenseCheckRequest = new oxOnlineLicenseCheckRequest();
        $oOnlineLicenseCheckRequest->edition = "edition";
        $oOnlineLicenseCheckRequest->version = "version";
        $oOnlineLicenseCheckRequest->shopurl = "shopUrl";
        $oOnlineLicenseCheckRequest->pversion = "pVersion";
        $oOnlineLicenseCheckRequest->productid = "productId";
        $oOnlineLicenseCheckRequest->revision = "revision";

        $oOnlineLicenseCheckRequest->keys = new stdClass();
        $oOnlineLicenseCheckRequest->keys->key = $aKeys;

        //expected xml file source
        $sXml = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
        $sXml.= '<olcRequest>';
        $sXml.= '<keys><key>'.$aKeys[0].'</key><key>'.$aKeys[1].'</key></keys>';
        $sXml.= '<revision>'.$oOnlineLicenseCheckRequest->revision.'</revision>';
        $sXml.= '<edition>'.$oOnlineLicenseCheckRequest->edition.'</edition>';
        $sXml.= '<version>'.$oOnlineLicenseCheckRequest->version.'</version>';
        $sXml.= '<shopurl>'.$oOnlineLicenseCheckRequest->shopurl.'</shopurl>';
        $sXml.= '<pversion>'.$oOnlineLicenseCheckRequest->pversion.'</pversion>';
        $sXml.= '<productid>'.$oOnlineLicenseCheckRequest->productid.'</productid>';
        $sXml.= '</olcRequest>'.PHP_EOL;

        $oOnlineLicenseCaller = new oxOnlineLicenseCaller();

        $this->assertEquals($sXml, $oOnlineLicenseCaller->generateXml($oOnlineLicenseCheckRequest));
    }

    public function testGenerateXml_whenPassingOneKeyThroughParameters()
    {
        $sKey = 'first key';

        $oRequestParams = new stdClass();
        $oRequestParams->edition = "edition";
        $oRequestParams->version = "version";
        $oRequestParams->revision = "revision";
        $oRequestParams->shopurl = "shopUrl";
        $oRequestParams->pversion = "pVersion";
        $oRequestParams->productid = "productId";

        $oRequestParams->keys = new stdClass();
        $oRequestParams->keys->key = $sKey;

        //expected xml file source
        $sXml = '<?xml version="1.0" encoding="utf-8"?>'.PHP_EOL;
        $sXml.= '<olcRequest>';
        $sXml.= '<edition>'.$oRequestParams->edition.'</edition>';
        $sXml.= '<version>'.$oRequestParams->version.'</version>';
        $sXml.= '<revision>'.$oRequestParams->revision.'</revision>';
        $sXml.= '<shopurl>'.$oRequestParams->shopurl.'</shopurl>';
        $sXml.= '<pversion>'.$oRequestParams->pversion.'</pversion>';
        $sXml.= '<productid>'.$oRequestParams->productid.'</productid>';
        $sXml.= '<keys><key>'.$sKey.'</key></keys>';
        $sXml.= '</olcRequest>'.PHP_EOL;

        $oOlc = $this->getProxyClass( 'oxOnlineLicenseCheck' );

        $this->assertEquals($sXml, $oOlc->UNITgenerateXml($oRequestParams));
    }
}
