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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Online VAT id checker class.
 */
class oxOnlineVatIdCheck extends oxCompanyVatInChecker
{

    /**
     * Keeps service check state
     *
     * @var bool
     */
    protected $_blServiceIsOn = null;

    /**
     * VAT check results cache
     *
     * @var array
     */
    protected static $_aVatCheckCache = array();

    /**
     * How many times to retry check if server is busy
     *
     */
    const BUSY_RETRY_CNT = 1;

    /**
     * How much to wait between retries (in micro seconds)
     *
     */
    const BUSY_RETRY_WAITUSEC = 500000;

    /**
     * Wsdl url
     *
     * @var string
     */
    protected $_sWsdl = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

    /**
     * Class constructor.
     */
    public function __construct()
    {
    }

    /**
     * Validates VAT.
     *
     * @param oxCompanyVatIn $oVatIn Company VAT identification number object.
     *
     * @return bool
     */
    public function validate(oxCompanyVatIn $oVatIn)
    {
        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = $oVatIn->getCountryCode();
        $oCheckVat->vatNumber = $oVatIn->getNumbers();

        $blResult = $this->_checkOnline($oCheckVat);
        if (!$blResult) {
            $this->setError('ID_NOT_VALID');
        }

        return $blResult;
    }

    /**
     * Parses error and throws exception for it
     *
     * @param string $sErrorMsg error message
     *
     * @deprecated since v5.2 (2014-07-28); This logic was moved to oxCompanyVatInValidator
     *
     * @throws oxConnectionException
     */
    protected function _parseError($sErrorMsg)
    {
        if (!$sErrorMsg || $sErrorMsg == 'INVALID_INPUT') {
            /** @var oxInputException $oEx */
            $oEx = oxNew('oxInputException');
            $oEx->setMessage('VAT_MESSAGE_' . ($sErrorMsg ? $sErrorMsg : 'ID_NOT_VALID'));
            throw $oEx;
        }

        /** @var oxConnectionException $oEx */
        $oEx = oxNew('oxConnectionException');
        $oEx->setAdress($this->getWsdlUrl());
        $oEx->setMessage('VAT_MESSAGE_' . $sErrorMsg);
        $oEx->debugOut();
        throw $oEx;
    }

    /**
     * Checks if USt.ID number is valid.
     * Returns true on success
     *
     * @param string $sCompVatId vat id that should be checked
     *
     * @deprecated since v5.2 (2014-07-28); use validate method
     *
     * @throws oxInputException, oxConnectionException
     * @return bool
     */
    public function checkUid($sCompVatId)
    {
        if (isset(self::$_aVatCheckCache[$sCompVatId])) {
            if (true === self::$_aVatCheckCache[$sCompVatId]) {
                return true;
            }
            if (is_string(self::$_aVatCheckCache[$sCompVatId])) {
                $this->_parseError(self::$_aVatCheckCache[$sCompVatId]);
            }

            return false;
        }

        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = substr($sCompVatId, 0, 2);
        $oCheckVat->vatNumber = substr($sCompVatId, 2);

        if (!$this->_checkOnline($oCheckVat)) {
            $sErrorMsg = $this->_getError();
            self::$_aVatCheckCache[$sCompVatId] = $sErrorMsg;
            $this->_parseError($sErrorMsg);
        }

        return self::$_aVatCheckCache[$sCompVatId] = true;
    }

    /**
     * Catches soap warning which is usually thrown due to service problems.
     * Return true and allows to continue process
     *
     * @param int    $iErrNo   error type number
     * @param string $sErrStr  error message
     * @param string $sErrFile error file
     * @param int    $iErrLine error line
     *
     * @return bool
     */
    public function catchWarning($iErrNo, $sErrStr, $sErrFile, $iErrLine)
    {
        // message to write to exception log
        $sLogMessage = "Warning: $sErrStr in $sErrFile on line $iErrLine";

        // fetching exception log file name
        $oEx = oxNew("oxException");
        $sLogFileName = $oEx->getLogFileName();

        // logs error message
        return oxRegistry::getUtils()->writeToLog($sLogMessage, $sLogFileName);
    }

    /**
     * Checks if VAT check can be performed:
     *  - if SoapClient class exists;
     *  - if service returns any output;
     *  - if output, returned by service, is valid.
     *
     * @return bool
     */
    protected function _isServiceAvailable()
    {
        if ($this->_blServiceIsOn === null) {
            $this->_blServiceIsOn = class_exists('SoapClient') ? true : false;
            if ($this->_blServiceIsOn) {
                $rFp = @fopen($this->getWsdlUrl(), 'r');
                $this->_blServiceIsOn = $rFp !== false;
                if ($this->_blServiceIsOn) {
                    $sWsdl = '';
                    while (!feof($rFp)) {
                        $sWsdl .= fread($rFp, 8192);
                    }
                    fclose($rFp);

                    // validating wsdl file
                    try {
                        $oDomDocument = new DOMDocument();
                        $oDomDocument->loadXML($sWsdl);
                    } catch (Exception $oExcp) {
                        // invalid xml
                        $this->_blServiceIsOn = false;
                    }
                }
            }
        }

        return $this->_blServiceIsOn;
    }

    /**
     * Checks online if USt.ID number is valid.
     * Returns true on success. On error sets error value.
     *
     * @param object $oCheckVat vat object
     *
     * @return bool
     */
    protected function _checkOnline($oCheckVat)
    {
        if ($this->_isServiceAvailable()) {
            $iTryMoreCnt = self::BUSY_RETRY_CNT;

            //T2009-07-02
            //how long socket should wait for server RESPONSE
            ini_set('default_socket_timeout', 5);

            // setting local error handler to catch possible soap errors
            set_error_handler(array($this, 'catchWarning'), E_WARNING);

            do {
                try {
                    //connection_timeout = how long we should wait to CONNECT to wsdl server
                    $oSoapClient = new SoapClient($this->getWsdlUrl(), array("connection_timeout" => 5));
                    $this->setError('');
                    $oRes = $oSoapClient->checkVat($oCheckVat);
                    $iTryMoreCnt = 0;
                } catch (SoapFault $e) {
                    $this->setError($e->faultstring);
                    if ($this->getError() == "SERVER_BUSY") {
                        usleep(self::BUSY_RETRY_WAITUSEC);
                    } else {
                        $iTryMoreCnt = 0;
                    }
                }
            } while (0 < $iTryMoreCnt--);

            // restoring previous error handler
            restore_error_handler();

            return (bool) $oRes->valid;
        } else {
            $this->setError("SERVICE_UNREACHABLE");

            return false;
        }
    }

    /**
     * Get error message.
     *
     * @deprecated since v5.2 (2014-07-28); This logic was moved to oxCompanyVatInValidator
     *
     * @return string
     */
    protected function _getError()
    {
        $sError = '';
        if ($this->_sError) {
            $sRegex = '/\{ \'([A-Z_]*)\' \}/';
            $n = preg_match($sRegex, $this->_sError, $aMatches);
            $sError = ($aMatches[1]) ? $aMatches[1] : 'SERVICE_UNAVAILABLE';
        }

        return $sError;
    }

    /**
     * Returns wsdl url
     *
     * @return string
     */
    public function getWsdlUrl()
    {
        // overriding wsdl url
        if (($sWsdl = oxRegistry::getConfig()->getConfigParam("sVatIdCheckInterfaceWsdl"))) {
            $this->_sWsdl = $sWsdl;
        }

        return $this->_sWsdl;
    }

    /**
     * Returns true if VAT id check is disabled
     *
     * @deprecated since v5.2 (2014-07-28); This logic was moved to oxCompanyVatInValidator
     *
     * @return bool
     */
    public function isDisabled()
    {
        return (bool) oxRegistry::getConfig()->getConfigParam("blVatIdCheckDisabled");
    }
}
