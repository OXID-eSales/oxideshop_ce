<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\EshopCommunity\Core\Exception\StandardException;
use \Exception;

/**
 * Class oxOnlineCaller makes call to given URL which is taken from child classes and sends request parameter.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
abstract class OnlineCaller
{
    const ALLOWED_HTTP_FAILED_CALLS_COUNT = 4;

    /** Amount of seconds for curl execution timeout. */
    const CURL_EXECUTION_TIMEOUT = 5;

    /** Amount of seconds for curl connect timeout. */
    const CURL_CONNECT_TIMEOUT = 3;

    /**
     * @var \OxidEsales\Eshop\Core\Curl
     */
    private $_oCurl;

    /**
     * @var \OxidEsales\Eshop\Core\OnlineServerEmailBuilder
     */
    private $_oEmailBuilder;

    /**
     * @var \OxidEsales\Eshop\Core\SimpleXml
     */
    private $_oSimpleXml;

    /**
     * Gets XML document name.
     *
     * @return string XML document tag name.
     */
    abstract protected function _getXMLDocumentName();

    /**
     * Gets service url.
     *
     * @return string Web service url.
     */
    abstract protected function _getServiceUrl();

    /**
     * Sets dependencies.
     *
     * @param \OxidEsales\Eshop\Core\Curl                     $oCurl         Sends request to OXID servers.
     * @param \OxidEsales\Eshop\Core\OnlineServerEmailBuilder $oEmailBuilder Forms email when OXID servers are unreachable.
     * @param \OxidEsales\Eshop\Core\SimpleXml                $oSimpleXml    Forms XML from Request for sending to OXID servers.
     */
    public function __construct(\OxidEsales\Eshop\Core\Curl $oCurl, \OxidEsales\Eshop\Core\OnlineServerEmailBuilder $oEmailBuilder, \OxidEsales\Eshop\Core\SimpleXml $oSimpleXml)
    {
        $this->_oCurl = $oCurl;
        $this->_oEmailBuilder = $oEmailBuilder;
        $this->_oSimpleXml = $oSimpleXml;
    }

    /**
     * Makes curl call with given parameters to given url.
     *
     * @param \OxidEsales\Eshop\Core\OnlineRequest $oRequest Information set in Request object will be sent to OXID servers.
     *
     * @return null|string In XML format.
     */
    public function call(\OxidEsales\Eshop\Core\OnlineRequest $oRequest)
    {
        $sOutputXml = null;
        $iFailedCallsCount = \OxidEsales\Eshop\Core\Registry::getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount');
        try {
            $sXml = $this->_formXMLRequest($oRequest);
            $sOutputXml = $this->_executeCurlCall($this->_getServiceUrl(), $sXml);
            $statusCode = $this->_getCurl()->getStatusCode();
            if ($statusCode != 200) {
                /** @var \OxidEsales\Eshop\Core\Exception\StandardException $oException */
                $oException = new StandardException('cUrl call to ' . $this->_getCurl()->getUrl() . ' failed with HTTP status '. $statusCode);
                throw $oException;
            }
            $this->_resetFailedCallsCount($iFailedCallsCount);
        } catch (Exception $oEx) {
            if ($iFailedCallsCount > self::ALLOWED_HTTP_FAILED_CALLS_COUNT) {
                $this->_castExceptionAndWriteToLog($oEx);
                $sXml = $this->_formEmail($oRequest);
                $this->_sendEmail($sXml);
                $this->_resetFailedCallsCount($iFailedCallsCount);
            } else {
                $this->_increaseFailedCallsCount($iFailedCallsCount);
            }
        }

        return $sOutputXml;
    }

    /**
     * Depending on the type of exception, first cast the exception and then write it to log.
     *
     * @deprecated since v6.3 (2018-04-25); This method will be removed completely. Use Registry::getLogger() to log error messages in the future.
     *
     * @param \Exception $oEx
     */
    protected function _castExceptionAndWriteToLog(\Exception $oEx)
    {
        if (!($oEx instanceof \OxidEsales\Eshop\Core\Exception\StandardException)) {
            $oOxException = oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class);
            $oOxException->setMessage($oEx->getMessage());
            $oOxException->debugOut();
        } else {
            $oEx->debugOut();
        }
    }

    /**
     * Forms email.
     *
     * @param \OxidEsales\Eshop\Core\OnlineRequest $oRequest Request object from which email should be formed.
     *
     * @return string
     */
    protected function _formEmail($oRequest)
    {
        return $this->_formXMLRequest($oRequest);
    }

    /**
     * Forms XML request.
     *
     * @param \OxidEsales\Eshop\Core\OnlineRequest $oRequest Request object from which server request should be formed.
     *
     * @return string
     */
    protected function _formXMLRequest($oRequest)
    {
        return $this->_getSimpleXml()->objectToXml($oRequest, $this->_getXMLDocumentName());
    }

    /**
     * Gets simple XML.
     *
     * @return \OxidEsales\Eshop\Core\SimpleXml
     */
    protected function _getSimpleXml()
    {
        return $this->_oSimpleXml;
    }

    /**
     * Gets curl.
     *
     * @return \OxidEsales\Eshop\Core\Curl
     */
    protected function _getCurl()
    {
        return $this->_oCurl;
    }

    /**
     * Gets email builder.
     *
     * @return \OxidEsales\Eshop\Core\OnlineServerEmailBuilder
     */
    protected function _getEmailBuilder()
    {
        return $this->_oEmailBuilder;
    }

    /**
     * Executes CURL call with given parameters.
     *
     * @param string $sUrl Server address to call to.
     * @param string $sXml Data to send. Currently OXID servers only accept XML formatted data.
     *
     * @return string
     */
    private function _executeCurlCall($sUrl, $sXml)
    {
        $oCurl = $this->_getCurl();
        $oCurl->setMethod('POST');
        $oCurl->setUrl($sUrl);
        $oCurl->setParameters(['xmlRequest' => $sXml]);
        $oCurl->setOption(
            \OxidEsales\Eshop\Core\Curl::EXECUTION_TIMEOUT_OPTION,
            static::CURL_EXECUTION_TIMEOUT
        );

        return $oCurl->execute();
    }

    /**
     * Sends an email with server information.
     *
     * @param string $sBody Mail content.
     */
    private function _sendEmail($sBody)
    {
        $oEmail = $this->_getEmailBuilder()->build($sBody);
        $oEmail->send();
    }

    /**
     * Resets config parameter iFailedOnlineCallsCount if it's bigger than 0.
     *
     * @param int $iFailedOnlineCallsCount Amount of calls which previously failed.
     */
    private function _resetFailedCallsCount($iFailedOnlineCallsCount)
    {
        if ($iFailedOnlineCallsCount > 0) {
            \OxidEsales\Eshop\Core\Registry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 0);
        }
    }

    /**
     * increases failed calls count.
     *
     * @param int $iFailedOnlineCallsCount Amount of calls which previously failed.
     */
    private function _increaseFailedCallsCount($iFailedOnlineCallsCount)
    {
        \OxidEsales\Eshop\Core\Registry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', ++$iFailedOnlineCallsCount);
    }
}
