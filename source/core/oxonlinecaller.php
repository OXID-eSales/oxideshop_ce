<?php

/**
 * Class oxOnlineCaller makes call to given URL address and send request parameter.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 *
 * @ignore   This class will not be included in documentation.
 */
abstract class oxOnlineCaller
{

    const ALLOWED_HTTP_FAILED_CALLS_COUNT = 4;

    /**
     * XML document tag name.
     *
     * @var string
     */
    protected $_sXMLDocumentName = 'onlineRequest';

    /**
     * Web service url.
     *
     * @var string
     */
    protected $_sServiceUrl;

    /**
     * @var oxCurl
     */
    private $_oCurl;

    /**
     * @var oxOnlineServerEmailBuilder
     */
    private $_oEmailBuilder;

    /**
     * @var oxSimpleXml
     */
    private $_oSimpleXml;

    /**
     * @param oxCurl $oCurl
     * @param oxOnlineServerEmailBuilder $oEmailBuilder
     * @param oxSimpleXml $oSimpleXml
     */
    public function __construct(oxCurl $oCurl, oxOnlineServerEmailBuilder $oEmailBuilder, oxSimpleXml $oSimpleXml)
    {
        $this->_oCurl = $oCurl;
        $this->_oEmailBuilder = $oEmailBuilder;
        $this->_oSimpleXml = $oSimpleXml;
    }

    /**
     * Get web service url.
     *
     * @return string
     */
    public function getWebServiceUrl()
    {
        return $this->_sServiceUrl;
    }

    /**
     * Set web service url.
     *
     * @param string $sUrl
     */
    public function setWebServiceUrl($sUrl)
    {
        $this->_sServiceUrl = $sUrl;
    }

    /**
     * Makes curl call with given parameters to given url.
     *
     * @param oxOnlineRequest $oRequest
     *
     * @return null|string In XML format.
     */
    public function call(oxOnlineRequest $oRequest)
    {
        $sOutputXml = null;
        $iFailedCallsCount = oxRegistry::getConfig()->getSystemConfigParameter('iFailedOnlineCallsCount');
        try {
            $sXml = $this->_formXMLRequest($oRequest);
            $sOutputXml = $this->_executeCurlCall($this->getWebServiceUrl(), $sXml);
            $this->_resetFailedCallsCount($iFailedCallsCount);
        } catch (Exception $oEx) {
            if ($iFailedCallsCount > self::ALLOWED_HTTP_FAILED_CALLS_COUNT) {
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
     * @param oxOnlineRequest $oRequest
     *
     * @return string
     */
    protected function _formEmail($oRequest)
    {
        return $this->_formXMLRequest($oRequest);
    }

    /**
     * @param oxOnlineRequest $oRequest
     *
     * @return string
     */
    protected function _formXMLRequest($oRequest)
    {
        return $this->_getSimpleXml()->objectToXml($oRequest, $this->_sXMLDocumentName);
    }

    /**
     * @return oxSimpleXml
     */
    protected function _getSimpleXml()
    {
        return $this->_oSimpleXml;
    }

    /**
     * Executes CURL call with given parameters.
     *
     * @param string $sUrl
     * @param string $sXml
     *
     * @return string
     */
    private function _executeCurlCall($sUrl, $sXml)
    {
        $oCurl = $this->_oCurl;
        $oCurl->setMethod('POST');
        $oCurl->setUrl($sUrl);
        $oCurl->setParameters(array('xmlRequest' => $sXml));
        $sOutput = $oCurl->execute();

        return $sOutput;
    }

    /**
     * Sends an email with server information.
     *
     * @param string $sBody
     */
    private function _sendEmail($sBody)
    {
        $oEmail = $this->_oEmailBuilder->build($sBody);
        $oEmail->send();
    }

    /**
     * Resets config parameter iFailedOnlineCallsCount if it's bigger than 0.
     *
     * @param int $iFailedOnlineCallsCount
     */
    private function _resetFailedCallsCount($iFailedOnlineCallsCount)
    {
        if ($iFailedOnlineCallsCount > 0) {
            oxRegistry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', 0);
        }
    }

    /**
     * @param int $iFailedOnlineCallsCount
     */
    private function _increaseFailedCallsCount($iFailedOnlineCallsCount)
    {
        oxRegistry::getConfig()->saveSystemConfigParameter('int', 'iFailedOnlineCallsCount', ++$iFailedOnlineCallsCount);
    }
}