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
     * Curl Options
     *
     * @var array
     */
    protected $_aOptions = array('CURLOPT_RETURNTRANSFER' => 1);


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
        $this->_sUrl = $sUrl;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->getMethod() == "GET" && $this->getQuery()) {
            $this->_sUrl = $this->_sUrl . "?" . $this->getQuery();
        }
        return $this->_sUrl;
    }

    /**
     * Set query like "param1=value1&param2=values2.."
     */
    public function setQuery( $sQuery = null )
    {
        if ( is_null($sQuery) ) {
            $sQuery = "";
            if ( $aParams = $this->getParameters() ) {
                $aParams = $this->_prepareQueryParameters( $aParams );
                $sQuery = http_build_query( $aParams, "", "&" );
            }
        }

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
            $this->setQuery();
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
        if ( is_null( $aHeader ) && $this->getMethod() == "POST") {
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
     * @return null
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
     *
     * @throws oxException on curl errors
     *
     * @return null
     */
    public function setOption( $sName, $sValue )
    {
        if (strpos( $sName, 'CURLOPT_' ) !== 0 || !defined($sConstant = strtoupper($sName))) {
            /**
             * @var oxException $oException
             */
            $oException = oxNew( 'oxException' );
            $oLang = oxRegistry::getLang();
            $oException->setMessage( sprintf( $oLang->translateString( 'EXCEPTION_NOT_VALID_CURL_CONSTANT', $oLang->getTplLanguage() ), $sName ) );
            throw $oException;
        }

        $this->_aOptions[$sName] = $sValue;
    }

    /**
     * Gets all options for a cURL transfer
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_aOptions;
    }

    /**
     * Executes curl call and returns response data as associative array.
     *
     * @throws oxException on curl errors
     *
     * @return string
     */
    public function execute()
    {
        $this->_setOptions();

        $sResponse = $this->_execute();
        $iCurlErrorNumber = $this->_getErrorNumber();

        $this->_close();

        if ( $iCurlErrorNumber ) {
            /**
             * @var oxException $oException
             */
            $oException = oxNew( 'oxException' );
            $oLang = oxRegistry::getLang();
            $oException->setMessage( sprintf( $oLang->translateString( 'EXCEPTION_CURL_ERROR', $oLang->getTplLanguage() ), $iCurlErrorNumber ) );
            throw $oException;
        }

        return $sResponse;
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
     * Set Curl Options
     */
    protected function _setOptions()
    {
        if (!is_null($this->getHeader())) {
            $this->_setOpt( CURLOPT_HTTPHEADER, $this->getHeader() );
        }
        $this->_setOpt( CURLOPT_URL, $this->getUrl() );

        if ( $this->getMethod() == "POST" ) {
            $this->_setOpt( CURLOPT_POST, 1 );
            $this->_setOpt( CURLOPT_POSTFIELDS, $this->getQuery() );
        }

        $aOptions = $this->getOptions();
        if ( count($aOptions) ) {
            foreach( $aOptions as $sName => $mValue  ) {
                $this->_setOpt( constant( $sName ), $mValue );
            }
        }
    }

    /**
     * Wrapper function to be mocked for testing.
     *
     * @return string
     */
    protected function _execute()
    {
        return curl_exec( $this->_getResource() );
    }

    /**
     * Wrapper function to be mocked for testing.
     *
     * @return null
     */
    protected function _close()
    {
        curl_close( $this->_getResource() );
        $this->_setResource( null );
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
     * Check if curl has errors. Set error message if has.
     *
     * @return int
     */
    protected function _getErrorNumber()
    {
        return curl_errno( $this->_getResource() );
    }

    /**
     * Clears empty values from array and decodes html entities.
     *
     * @param $aParams
     * @return array
     */
    protected function _prepareQueryParameters( $aParams )
    {
        $aParams = array_filter( $aParams );
        $aParams = array_map( array( $this, '_htmlDecode' ), $aParams );

        return $aParams;
    }

    /**
     * Decode (if needed) html entity.
     *
     * @param mixed $mParam query
     *
     * @return string
     */
    protected function _htmlDecode( $mParam )
    {
        if ( is_array( $mParam ) ) {
            $mParam = $this->_prepareQueryParameters( $mParam );
        } else {
            $mParam = html_entity_decode( stripslashes( $mParam ), ENT_QUOTES, $this->getConnectionCharset() );
        }

        return $mParam;
    }

}