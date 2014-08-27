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
 * Class oxOnlineLicenseCheckCaller
 *
 * @internal Do not make a module extension for this class.
 * @see http://www.oxid-forge.com/do_not_extend_classes_list/
 *
 * @ignore This class will not be included in documentation.
 */
class oxOnlineLicenseCheckCaller
{
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
    protected $_sServiceUrl = 'https://olc.oxid-esales.com/check.php';

    /**
     * Expected response element in the XML response message fom web service.
     *
     * @var string
     */
    protected $_sResponseElement = 'olc';

    /**
     * @var oxOnlineCaller
     */
    private $_oOnlineCaller;

    /**
     * @var oxSimpleXml
     */
    private $_oSimpleXml;

    /**
     * @param oxOnlineCaller $oOnlineCaller
     * @param oxSimpleXml $oSimpleXml
     */
    public function __construct(oxOnlineCaller $oOnlineCaller = null, oxSimpleXml $oSimpleXml = null)
    {
        if (is_null($oOnlineCaller)) {
            $oCurl = oxNew('oxCurl');
            $oOnlineCaller = oxNew('oxOnlineCaller', $oCurl);
        }
        $this->_oOnlineCaller = $oOnlineCaller;

        if (is_null($oSimpleXml)) {
            $oSimpleXml = oxNew('oxSimpleXml');
        }
        $this->_oSimpleXml = $oSimpleXml;
    }

    /**
     * Get Online License Key Check web service url.
     *
     * @return string
     */
    public function getWebServiceUrl()
    {
        return $this->_sServiceUrl;
    }

    /**
     * Performs Web service request
     *
     * @param oxOnlineLicenseServerRequest $oRequest Object with request parameters
     *
     * @throws oxException
     * @return oxOnlineLicenseCheckResponse
     */
    public function doRequest(oxOnlineLicenseServerRequest $oRequest)
    {
        $oSimpleXml = $this->_getSimpleXml();
        $sRequest = $oSimpleXml->objectToXml($oRequest, 'olcRequest');

        $oCaller = $this->_getOnlineCaller();
        $sResponse = $oCaller->call($sRequest, $this->getWebServiceUrl());

        return $this->_formResponse($sResponse);
    }

    /**
     * @return oxOnlineCaller
     */
    protected function _getOnlineCaller()
    {
        return $this->_oOnlineCaller;
    }

    /**
     * @return oxSimpleXml
     */
    protected function _getSimpleXml()
    {
        return $this->_oSimpleXml;
    }

    /**
     * Parse response message received from Online License Key Check web service and save it to response object.
     *
     * @param string $sRawResponse
     * @throws oxException
     * @return oxOnlineLicenseCheckResponse
     */
    protected function _formResponse($sRawResponse)
    {
        /** @var oxUtilsXml $oUtilsXml */
        $oUtilsXml = oxRegistry::get( "oxUtilsXml" );
        if ( empty($sRawResponse) || !($oDomDoc = $oUtilsXml->loadXml( $sRawResponse )) ) {
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

        /** @var oxOnlineLicenseCheckResponse $oResponse */
        $oResponse = oxNew('oxOnlineLicenseCheckResponse');

        // iterate through response node to get response parameters
        for ( $i = 0; $i < $oNodes->length; $i++ ) {
            $sNodeName = $oNodes->item( $i )->nodeName;
            $sNodeValue = $oNodes->item( $i )->nodeValue;
            $oResponse->$sNodeName = $sNodeValue;
        }

        return $oResponse;
    }
}