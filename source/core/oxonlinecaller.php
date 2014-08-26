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
     * @param $sUrl
     * @param $aCurlParameters
     *
     * @return null|string
     *
     * @throws oxException
     */
    public function call($sUrl, $aCurlParameters)
    {
        $sOutput = null;
        $iFailedOnlineCallsCount = oxRegistry::getConfig()->getConfigParam('iFailedOnlineCallsCount');
        try {
            $oCurl = $this->_getCurl();
            $oCurl->setMethod('POST');
            $oCurl->setUrl($sUrl);
            $oCurl->setParameters($aCurlParameters);
            $sOutput = $oCurl->execute();
            $this->_resetFailedCallsCount($iFailedOnlineCallsCount);
        } catch (Exception $oEx) {
            if ($iFailedOnlineCallsCount >= 5) {
                throw new oxException('OLC_ERROR_REQUEST_FAILED');
            }
            oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', ++$iFailedOnlineCallsCount);
        }

        return $sOutput;
    }

    /**
     * @return oxCurl
     */
    protected function _getCurl()
    {
        return $this->_oCurl;
    }

    /**
     * Resets config parameter iFailedOnlineCallsCount if it's bigger than 0.
     *
     * @param $iFailedOnlineCallsCount
     */
    private function _resetFailedCallsCount($iFailedOnlineCallsCount)
    {
        if ($iFailedOnlineCallsCount > 0) {
            oxRegistry::getConfig()->setConfigParam('iFailedOnlineCallsCount', 0);
        }
    }
}