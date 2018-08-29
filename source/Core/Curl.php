<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * CURL request handler.
 * Handles CURL calls
 */
class Curl
{
    /** Curl option for setting the timeout of whole execution process. */
    const EXECUTION_TIMEOUT_OPTION = 'CURLOPT_TIMEOUT';

    /** Curl option for setting the timeout for connect. */
    const CONNECT_TIMEOUT_OPTION = 'CURLOPT_CONNECTTIMEOUT';

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
     *
     * @var string
     */
    protected $_sConnectionCharset = "UTF-8";

    /**
     * Curl call header.
     *
     * @var array
     */
    protected $_aHeader = null;

    /**
     * Host for header.
     *
     * @var string
     */
    protected $_sHost = null;

    /**
     * Curl Options
     *
     * @var array
     */
    protected $_aOptions = ['CURLOPT_RETURNTRANSFER' => 1];

    /**
     * Request HTTP status call code.
     *
     * @var int | null
     */
    protected $_sStatusCode = null;

    /**
     * Sets url to call
     *
     * @param string $url URL to call.
     *
     * @throws oxException if url is not valid
     */
    public function setUrl($url)
    {
        $this->_sUrl = $url;
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
     *
     * @param string $query Request query.
     */
    public function setQuery($query)
    {
        $this->_sQuery = $query;
    }

    /**
     * Builds query like "param1=value1&param2=values2.."
     *
     * @return string
     */
    public function getQuery()
    {
        if (is_null($this->_sQuery)) {
            $query = "";
            if ($params = $this->getParameters()) {
                $params = $this->_prepareQueryParameters($params);
                $query = http_build_query($params, "", "&");
            }
            $this->setQuery($query);
        }

        return $this->_sQuery;
    }

    /**
     * Sets parameters to be added to call url.
     *
     * @param array $parameters parameters
     */
    public function setParameters($parameters)
    {
        $this->setQuery(null);
        $this->_aParameters = $parameters;
    }

    /**
     * Return parameters to be added to call url.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->_aParameters;
    }

    /**
     * Sets host.
     *
     * @param string $host
     */
    public function setHost($host)
    {
        $this->_sHost = $host;
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
     * @param array $header
     */
    public function setHeader($header = null)
    {
        if (is_null($header) && $this->getMethod() == "POST") {
            $host = $this->getHost();

            $header = [];
            $header[] = 'POST /cgi-bin/webscr HTTP/1.1';
            $header[] = 'Content-Type: application/x-www-form-urlencoded';
            if (isset($host)) {
                $header[] = 'Host: ' . $host;
            }
            $header[] = 'Connection: close';
        }
        $this->_aHeader = $header;
    }

    /**
     * Forms header from host.
     *
     * @return array
     */
    public function getHeader()
    {
        if (is_null($this->_aHeader)) {
            $this->setHeader();
        }

        return $this->_aHeader;
    }

    /**
     * Set method to send (POST/GET)
     *
     * @param string $method method to send (POST/GET)
     */
    public function setMethod($method)
    {
        $this->_sMethod = strtoupper($method);
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
     * @param string $name  curl option name to set value to.
     * @param string $value curl option value to set.
     *
     * @throws oxException on curl errors
     */
    public function setOption($name, $value)
    {
        if (strpos($name, 'CURLOPT_') !== 0 || !defined($constant = strtoupper($name))) {
            /**
             * @var \OxidEsales\Eshop\Core\Exception\StandardException $exception
             */
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class);
            $lang = \OxidEsales\Eshop\Core\Registry::getLang();
            $exception->setMessage(sprintf($lang->translateString('EXCEPTION_NOT_VALID_CURL_CONSTANT', $lang->getTplLanguage()), $name));
            throw $exception;
        }

        $this->_aOptions[$name] = $value;
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

        $response = $this->_execute();
        $this->_saveStatusCode();

        $curlErrorNumber = $this->_getErrorNumber();

        $this->_close();

        if ($curlErrorNumber) {
            /**
             * @var \OxidEsales\Eshop\Core\Exception\StandardException $exception
             */
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class);
            $lang = \OxidEsales\Eshop\Core\Registry::getLang();
            $exception->setMessage(sprintf($lang->translateString('EXCEPTION_CURL_ERROR', $lang->getTplLanguage()), $curlErrorNumber));
            throw $exception;
        }

        return $response;
    }

    /**
     * Set connection charset
     *
     * @param string $charset charset
     */
    public function setConnectionCharset($charset)
    {
        $this->_sConnectionCharset = $charset;
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
     * Return HTTP status code.
     *
     * @return int HTTP status code.
     */
    public function getStatusCode()
    {
        return $this->_sStatusCode;
    }

    /**
     * Sets resource
     *
     * @param resource $rCurl curl.
     */
    protected function _setResource($rCurl)
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
        if (is_null($this->_rCurl)) {
            $this->_setResource(curl_init());
        }

        return $this->_rCurl;
    }

    /**
     * Set Curl Options
     */
    protected function _setOptions()
    {
        if (!is_null($this->getHeader())) {
            $this->_setOpt(CURLOPT_HTTPHEADER, $this->getHeader());
        }
        $this->_setOpt(CURLOPT_URL, $this->getUrl());

        if ($this->getMethod() == "POST") {
            $this->_setOpt(CURLOPT_POST, 1);
            $this->_setOpt(CURLOPT_POSTFIELDS, $this->getQuery());
        }

        $options = $this->getOptions();
        if (count($options)) {
            foreach ($options as $name => $mValue) {
                $this->_setOpt(constant($name), $mValue);
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
        return curl_exec($this->_getResource());
    }

    /**
     * Wrapper function to be mocked for testing.
     */
    protected function _close()
    {
        curl_close($this->_getResource());
        $this->_setResource(null);
    }

    /**
     * Wrapper function to be mocked for testing.
     *
     * @param string $name  curl option name to set value to.
     * @param string $value curl option value to set.
     */
    protected function _setOpt($name, $value)
    {
        curl_setopt($this->_getResource(), $name, $value);
    }

    /**
     * Check if curl has errors. Set error message if has.
     *
     * @return int
     */
    protected function _getErrorNumber()
    {
        return curl_errno($this->_getResource());
    }

    /**
     * Sets current request HTTP status code.
     */
    protected function _saveStatusCode()
    {
        $this->_sStatusCode = curl_getinfo($this->_getResource(), CURLINFO_HTTP_CODE);
    }

    /**
     * Decodes html entities.
     *
     * @param array $params Parameters.
     *
     * @return array
     */
    protected function _prepareQueryParameters($params)
    {
        return array_map([$this, '_htmlDecode'], array_filter($params));
    }

    /**
     * Decode (if needed) html entity.
     *
     * @param mixed $mParam query
     *
     * @return string
     */
    protected function _htmlDecode($mParam)
    {
        if (is_array($mParam)) {
            $mParam = $this->_prepareQueryParameters($mParam);
        } else {
            $mParam = html_entity_decode(stripslashes($mParam), ENT_QUOTES, $this->getConnectionCharset());
        }

        return $mParam;
    }
}
