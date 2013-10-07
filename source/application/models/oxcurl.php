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

class oxCurl
{
    /**
     * Curl instance.
     *
     * @var resource
     */
    protected $_rCurl = null;

    /**
     * URL to call
     *
     * @var string | null
     */
    protected $_sUrl = null;

    /**
     * Query like "param1=value1&param2=values2.."
     *
     * @return string
     */
    protected $_sQuery = null;

    /**
     * Set CURL method
     *
     * @return string
     */
    protected $_sMethod = 'POST';

    /**
     * Parameter to be added to call url
     *
     * @var array | null
     */
    protected $_aParameters = null;

    /**
     * Connection Charset.
     * @var string
     */
    protected $_sConnectionCharset = "UTF-8";

    /**
     * Curl call header.
     * @var array
     */
    protected $_aHeader = null;

    /**
     * Host for header.
     * @var string
     */
    protected $_sHost = null;

    /**
     * Sets resource
     *
     * @param resource $rCurl curl.
     */
    protected function _setResource( $rCurl )
    {
        $this->_rCurl = $rCurl;
        }

    /**
     * Returns curl resource
     *
     * @return resource
     */
    protected function _getResource()
    {
        if ( is_null( $this->_rCurl ) ) {
            $this->_setResource( curl_init() );
        }

        return $this->_rCurl;
    }

    /**
     * Sets url to call
     *
     * @param string $sUrl URL to call.
     *
     * @throws oxException if url is not valid
     *
     * @return null
     */
    public function setUrl( $sUrl )
    {
        if ( false === filter_var( $sUrl, FILTER_VALIDATE_URL ) ) {
            /**
             * @var oxException $oException
             */
            $oException = oxNew( "oxException", 'EXCEPTION_NOT_VALID_URL' );
            throw $oException;
        }
        $this->_sUrl = $sUrl;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_sUrl;
        }

    /**
     * Set query like "param1=value1&param2=values2.."
     */
    public function setQuery( $sQuery )
    {
        $this->_sQuery = $sQuery;
    }

    /**
     * Builds query like "param1=value1&param2=values2.."
     *
     * @return string
     */
    public function getQuery()
    {
        if ( is_null( $this->_sQuery ) ) {

            $aParams = $this->getParameters();
            $aParams = array_filter( $aParams );
            $aParams = array_map(array($this, '_htmlDecode'), $aParams);

            $this->setQuery( http_build_query( $aParams, "", "&" ) );
        }

        return $this->_sQuery;
    }

    /**
     * Sets parameters to be added to call url.
     *
     * @param array $aParameters parameters
     */
    public function setParameters( $aParameters )
    {
        $this->_aParameters = $aParameters;
    }

    /**
     * Return parameters to be added to call url.
     *
     * return array
     */
    public function getParameters()
    {
        return $this->_aParameters;
        }

    /**
     * Set connection charset
     *
     * @param string $sCharset charset
     */
    public function setConnectionCharset( $sCharset )
    {
        $this->_sConnectionCharset = $sCharset;
    }

    /**
     * Return connection charset
     *
     * @return string
     */
    public function getConnectionCharset()
    {
        return $this->_sConnectionCharset;
    }

    /**
     * Sets host.
     *
     * @param string $sHost
     *
     * @return null
     */
    public function setHost( $sHost )
    {
        $this->_sHost = $sHost;
    }

    /**
     * Returns host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->_sHost;
    }

    /**
     * Set header.
     *
     * @param array $aHeader
     *
     * @return null
     */
    public function setHeader( $aHeader = null )
    {
        if ( is_null( $aHeader ) ) {
            $sHost = $this->getHost();

            $aHeader = array();
            $aHeader[] = 'POST /cgi-bin/webscr HTTP/1.1';
            $aHeader[] = 'Content-Type: application/x-www-form-urlencoded';
            if ( isset( $sHost ) ) {
                $aHeader[] = 'Host: '. $sHost;
            }
            $aHeader[] = 'Connection: close';
        }
        $this->_aHeader = $aHeader;
    }

    /**
     * Forms header from host.
     *
     * @return array
     */
    public function getHeader()
    {
        if ( is_null( $this->_aHeader ) ) {
            $this->setHeader();
        }
        return $this->_aHeader;
    }

    /**
     * Set method to send (POST/GET)
     *
     * @param string $sMethod method to send (POST/GET)
     *
     * @return CurlWrapper
     */
    public function setMethod($sMethod)
    {
        $this->_sMethod = strtoupper($sMethod);
    }

    /**
     * Return method to send
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->_sMethod;
    }

    /**
     * Sets an option for a cURL transfer
     *
     * @param string $sName  curl option name to set value to.
     * @param string $sValue curl option value to set.
     */
    public function setOption( $sName, $sValue )
    {
        if (strpos( $sName, 'CURLOPT_' ) !== 0 || !defined($sConstant = strtoupper($sName))) {
            /**
             * @var oxException $oException
             */
            $oException = oxNew( "oxException", 'EXCEPTION_NOT_VALID_CONSTANT' );
            throw $oException;
        }

        $this->_setOpt( $sName, $sValue );
    }

    /**
     * Wrapper function to be mocked for testing.
     *
     * @param string $sName  curl option name to set value to.
     * @param string $sValue curl option value to set.
     */
    protected function _setOpt( $sName, $sValue )
    {
        curl_setopt( $this->_getResource(), $sName, $sValue );
    }

    /**
     * Set Curl Options.
     */
    protected function _setOptions()
    {
       /* foreach( $this->getEnvironmentParameters() as $sName => $mValue  ) {
            $this->setOption( constant( $sName ), $mValue );
        }*/

        $this->_setOpt( CURLOPT_HTTPHEADER, $this->getHeader() );
        $this->_setOpt( CURLOPT_URL, $this->getUrl() );
        $this->_setOpt( CURLOPT_POSTFIELDS, $this->getQuery() );
    }

    /**
     * Decode (if needed) html entity
     *
     * @param string $sString query
     *
     * @return string
     */
    protected function _htmlDecode( $sString )
    {
        $sString = html_entity_decode( stripslashes( $sString ), ENT_QUOTES, $this->getConnectionCharset() );

        return $sString;
    }

}