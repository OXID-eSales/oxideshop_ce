<?php

/**
 * Class oxOnlineCaller makes call to given URL address and send request parameter.
 *
 * @internal Do not make a module extension for this class.
 * @see http://www.oxid-forge.com/do_not_extend_classes_list/
 *
 * @ignore This class will not be included in documentation.
 */
class oxOnlineCaller
{
    const ALLOWED_HTTP_FAILED_CALLS_COUNT = 4;

    /**
     * @var oxCurl
     */
    private $_oCurl;

    /**
     * @param oxCurl $oCurl
     */
    public function __construct(oxCurl $oCurl)
    {
        $this->_oCurl = $oCurl;
    }

    /**
     * Makes curl call with given parameters to given url.
     *
     * @param string $sUrl
     * @param string $sXml
     *
     * @return null|string In XML format.
     *
     * @throws oxException When calls count is bigger than allowed calls count.
     */
    public function call($sUrl, $sXml)
    {
        $sOutputXml = null;
        $iFailedCallsCount = oxRegistry::getConfig()->getConfigParam('iFailedOnlineCallsCount');
        try {
            $sOutputXml = $this->_executeCurlCall($sUrl, $sXml);
            $this->_resetFailedCallsCount($iFailedCallsCount);
        } catch (Exception $oEx) {
            if ($iFailedCallsCount > self::ALLOWED_HTTP_FAILED_CALLS_COUNT) {
                throw new oxException('OLC_ERROR_REQUEST_FAILED');
            }
            $this->_increaseFailedCallsCount($iFailedCallsCount);
        }

        return $sOutputXml;
    }

    /**
     * Resets config parameter iFailedOnlineCallsCount if it's bigger than 0.
     *
     * @param int $iFailedOnlineCallsCount
     */
    private function _resetFailedCallsCount($iFailedOnlineCallsCount)
    {
        if ($iFailedOnlineCallsCount > 0) {
            oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', 0);
        }
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
     * @param int $iFailedOnlineCallsCount
     */
    private function _increaseFailedCallsCount($iFailedOnlineCallsCount)
    {
        oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', ++$iFailedOnlineCallsCount);
    }
}