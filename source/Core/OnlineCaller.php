<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\EshopCommunity\Core\Exception\StandardException;
use Exception;

/**
 * Class oxOnlineCaller makes call to given URL which is taken from child classes and sends request parameter.
 *
 * @internal Do not make a module extension for this class.
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
    abstract protected function getXMLDocumentName();
    /**
     * Gets service url.
     *
     * @return string Web service url.
     */
    abstract protected function getServiceUrl();

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
            $sXml = $this->formXMLRequest($oRequest);
            $sOutputXml = $this->executeCurlCall($this->getServiceUrl(), $sXml);
            $statusCode = $this->getCurl()->getStatusCode();
            if ($statusCode != 200) {
                /** @var \OxidEsales\Eshop\Core\Exception\StandardException $oException */
                $oException = new StandardException('cUrl call to ' . $this->getCurl()->getUrl() . ' failed with HTTP status ' . $statusCode);
                throw $oException;
            }
            $this->resetFailedCallsCount($iFailedCallsCount);
        } catch (Exception $oEx) {
            if ($iFailedCallsCount > self::ALLOWED_HTTP_FAILED_CALLS_COUNT) {
                \OxidEsales\Eshop\Core\Registry::getLogger()->error($oEx->getMessage(), [$oEx]);

                $sXml = $this->formEmail($oRequest);
                $this->sendEmail($sXml);
                $this->resetFailedCallsCount($iFailedCallsCount);
            } else {
                $this->increaseFailedCallsCount($iFailedCallsCount);
            }
        }

        return $sOutputXml;
    }

    /**
     * Forms email.
     *
     * @param \OxidEsales\Eshop\Core\OnlineRequest $oRequest Request object from which email should be formed.
     *
     * @return string
     */
    protected function formEmail($oRequest)
    {
        return $this->formXMLRequest($oRequest);
    }

    /**
     * Forms XML request.
     *
     * @param \OxidEsales\Eshop\Core\OnlineRequest $oRequest Request object from which server request should be formed.
     *
     * @return string
     */
    protected function formXMLRequest($oRequest)
    {
        return $this->getSimpleXml()->objectToXml($oRequest, $this->getXMLDocumentName());
    }

    /**
     * Gets simple XML.
     *
     * @return \OxidEsales\Eshop\Core\SimpleXml
     */
    protected function getSimpleXml()
    {
        return $this->_oSimpleXml;
    }

    /**
     * Gets curl.
     *
     * @return \OxidEsales\Eshop\Core\Curl
     */
    protected function getCurl()
    {
        return $this->_oCurl;
    }

    /**
     * Gets email builder.
     *
     * @return \OxidEsales\Eshop\Core\OnlineServerEmailBuilder
     */
    protected function getEmailBuilder()
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
    private function executeCurlCall($sUrl, $sXml)
    {
        $oCurl = $this->getCurl();
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
    private function sendEmail($sBody)
    {
        $oEmail = $this->getEmailBuilder()->build($sBody);
        $oEmail->send();
    }

    /**
     * Resets config parameter iFailedOnlineCallsCount if it's bigger than 0.
     *
     * @param int $iFailedOnlineCallsCount Amount of calls which previously failed.
     */
    private function resetFailedCallsCount($iFailedOnlineCallsCount)
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
    private function increaseFailedCallsCount($iFailedOnlineCallsCount)
    {
        \OxidEsales\Eshop\Core\Registry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', ++$iFailedOnlineCallsCount);
    }
}
