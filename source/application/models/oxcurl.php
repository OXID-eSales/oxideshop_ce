<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * CURL request handler.
 * Handles CURL calls
 *
 * @package model
 */

class oxCurl {

    /**
     * Export output folder
     *
     * @var string
     */
    protected $_iCurlTimeout = 30;

    /**
     * Web service script
     *
     * @var string
     */
    protected $_sWebServiceUrl = '';

    /**
     * Web service script
     *
     * @var array
     */
    protected $_aWebServiceParams = array();

    /**
     * CRUL time out setter
     *
     * @param $iSeconds integer
     */
    public function setTimeout( $iSeconds )
    {
        if ( $iSeconds > 0 ) {
            $this->_iCurlTimeout = $iSeconds;
        }
    }

    /**
     * CRUL time out getter
     *
     * @return integer
     */
    public function getTimeout()
    {
        return $this->_iCurlTimeout;
    }

    /**
     * web service URL setter
     *
     * @param $sServiceUrl string
     */
    public function setWebServiceUrl( $sServiceUrl )
    {
        if ( !empty( $sServiceUrl ) ) {
            $this->_sWebServiceUrl = $sServiceUrl;
        }
    }

    /**
     * web service URL setter
     *
     * @return string
     */
    public function getWebServiceUrl()
    {
        return $this->_sWebServiceUrl;
    }


    /**
     * web service URL setter
     *
     * @param $aServiceParams array of parameters in format [key = > value]
     */
    public function setWebServiceParams( $aServiceParams )
    {
        if ( !empty( $aServiceParams ) ) {
            $this->_aWebServiceParams = $aServiceParams;
        }
    }

    /**
     * web service url getter
     *
     * @param null $aServiceParams - optional  web service parameters can be set here instead of setter
     * @return string
     */
    public function getWebServiceRequestUrl( $aServiceParams = null )
    {
        return $this->_sWebServiceUrl . '?' . http_build_query( ( $aServiceParams != null ) ? $aServiceParams : $this->_aWebServiceParams );
    }


    /**
     * @param null $aServiceParams - optional  web service parameters can be set here instead of setter
     * @return string XML
     */
    public function callWebService( $aServiceParams = null )
    {
        $curl = curl_init();
        curl_setopt( $curl,CURLOPT_URL, $this->getWebServiceRequestUrl( $aServiceParams ) );
        curl_setopt( $curl,CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $curl,CURLOPT_CONNECTTIMEOUT, $this->_iCurlTimeout );
        $sResponse = curl_exec( $curl );
        curl_close( $curl );

        return $sResponse;
    }


}