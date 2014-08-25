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
 * Class oxOnlineLicenseCaller
 *
 * @internal Do not make a module extension for this class.
 * @see http://www.oxid-forge.com/do_not_extend_classes_list/
 *
 * @ignore This class will not be included in documentation.
 */
class oxOnlineLicenseCaller
{

    /**
     * Online License Check service name.
     */
    const SERVICE_ONLINE_LICENSE_CHECK = 'OLC';

    /**
     * Online Module Version Notifier service name.
     */
    const SERVICE_MODULE_VERSION_NOTIFIER = 'OMVN';

    /**
     * Raw response message received from Online License Key Check web service.
     *
     * @var string
     */
    protected $_sRawResponseMessage = '';

    /**
     * Online License Key Check web service url.
     *
     * @var string
     */
    protected $_sOnlineLicenseCheckWebServiceUrl = 'https://olc.oxid-esales.com/check.php';

    /**
     * Online Module Version Notifier web service url.
     *
     * @var string
     */
    protected $_sModuleVersionNotifierWebServiceUrl = 'https://omvn.oxid-esales.com/check.php';

    /**
     * Expected response element in the XML response message fom web service.
     *
     * @var string
     */
    protected $_sResponseElement = 'olc';

    private $_oCurl;

    /**
     * Parsed response object.
     *
     * @var object
     */
    protected $_oResponse = null;

    /**
     * OLC or OMVN.
     *
     * @var string
     */
    private $_sWebServiceUrlType;


    public function __construct(oxCurl $oCurl = null)
    {
        if (is_null($oCurl)) {
            $oCurl = oxNew('oxCurl');
        }
        $this->_oCurl = $oCurl;
    }

    /**
     * Set raw response message received from Online License Key Check web service.
     *
     * @param $sRawResponseMessage string
     */
    public function setRawResponseMessage( $sRawResponseMessage )
    {
        $this->_sRawResponseMessage = $sRawResponseMessage;
    }

    /**
     * Get raw response message received from Online License Key Check web service.
     *
     * @return string
     */
    public function getRawResponseMessage()
    {
        return $this->_sRawResponseMessage;
    }

    /**
     * Get Online License Key Check web service url.
     *
     * @return string
     */
    public function getWebServiceUrl()
    {
        if ($this->_sWebServiceUrlType != self::SERVICE_ONLINE_LICENSE_CHECK) {
            $sWebServiceUrl = $this->_sModuleVersionNotifierWebServiceUrl;
        } else {
            $sWebServiceUrl = $this->_sOnlineLicenseCheckWebServiceUrl;
        }

        return $sWebServiceUrl;

    }

    /**
     * Set response object.
     *
     * @param $oResponse object
     */
    public function setResponse( $oResponse )
    {
        $this->_oResponse = $oResponse;
    }

    /**
     * Get response object.
     *
     * @return object
     */
    public function getResponse()
    {
        if ( is_null( $this->_oResponse ) ) {
            $this->setResponse( new stdClass() );
        }

        return $this->_oResponse;
    }

    /**
     * Performs Web service request
     *
     * @param oxOnlineLicenseServerRequest $oRequest Object with request parameters
     *
     * @param string $sWebServiceUrlType This is used to select web service url, possible options:
     * OLC, OMVN.
     * @throws oxException
     * @return string
     */
    public function doRequest(oxOnlineLicenseServerRequest $oRequest, $sWebServiceUrlType)
    {
        $this->_sWebServiceUrlType = $sWebServiceUrlType;
        $sXml = $this->_generateXml($oRequest);
        // send request to web service
        try {
            $oCurl = $this->_getCurl();
            $oCurl->setMethod( 'POST' );
            $oCurl->setUrl( $this->getWebServiceUrl() );
            $oCurl->setParameters( array("xmlRequest" => $sXml) );
            $sOutput = $oCurl->execute();
        } catch (Exception $oEx) {
            throw new oxException($this->_sWebServiceUrlType.'_ERROR_REQUEST_FAILED');
        }

        return $sOutput;
    }

    /**
     * Parse response message received from Online License Key Check web service and save it to response object.
     *
     * @throws oxException
     */
    public function getParsedResponseMessage()
    {
        $sRawResponse = $this->getRawResponseMessage();
        $oUtilsXml = oxRegistry::get( "oxUtilsXml" );
        if ( !($oDomDoc = $oUtilsXml->loadXml( $sRawResponse )) ) {
            throw new oxException('OLC_ERROR_RESPONSE_NOT_VALID');
        }

        if ( $oDomDoc->documentElement->nodeName != $this->_sResponseElement ) {
            throw new oxException('OLC_ERROR_RESPONSE_UNEXPECTED');
        }

        $oResponseNode = $oDomDoc->firstChild;

        if ( !$oResponseNode->hasChildNodes() ) {
            throw new oxException('OLC_ERROR_RESPONSE_NOT_VALID');
        }

        $oNodes = $oResponseNode->childNodes;

        $oResponse = $this->getResponse();

        // iterate through response node to get response parameters
        for ( $i = 0; $i < $oNodes->length; $i++ ) {
            $sNodeName = $oNodes->item( $i )->nodeName;
            $sNodeValue = $oNodes->item( $i )->nodeValue;
            $oResponse->$sNodeName = $sNodeValue;
        }

        return $oResponse;
    }

    /**
     * Generates Xml from passed object parameters
     *
     * @param object $oRequestParams Object with request parameters
     * @return string
     */
    protected function _generateXml($oRequestParams)
    {
        $oXml = oxNew('oxSimpleXml');
        $sXml = $oXml->objectToXml($oRequestParams, 'olcRequest');

        return $sXml;
    }

    /**
     * @return oxCurl
     */
    protected function _getCurl()
    {
        return $this->_oCurl;
    }
}