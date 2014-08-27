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
 * Performs Online License Key check
 *
 * @internal Do not make a module extension for this class.
 * @see http://www.oxid-forge.com/do_not_extend_classes_list/
 *
 * @ignore This class will not be included in documentation.
 */
class oxOnlineLicenseCheck
{
    /**
     * Variable name to be used in oxConfig table
     */
    const CONFIG_VAR_NAME = 'iOlcSuccess';

    /**
     * Expected valid response code.
     *
     * @var integer
     */
    protected $_iValidResponseCode = 0;

    /**
     * Expected valid response message.
     *
     * @var string
     */
    protected $_sValidResponseMessage = 'ACK';

    /**
     * License key validation result.
     *
     * @var bool
     */
    protected $_blValidationResult = null;

    /**
     * List of serial keys to validate.
     *
     * @var array
     */
    protected $_aSerialKeys = array();

    /**
     * Error message for the user.
     *
     * @var string
     */
    protected $_sErrorMessage = '';

    /**
     * Indicates exception event
     *
     * @var bool
     */
    protected $_blIsException = false;

    /**
     * @var oxOnlineLicenseCheckCaller
     */
    protected $_oCaller = null;

    /**
     * @param null $oCaller
     */
    public function __construct($oCaller)
    {
        $this->_oCaller = $oCaller;
    }

    /**
     * Get error message.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_sErrorMessage;
    }

    /**
     * Get license key validation result.
     *
     * @return bool
     */
    public function getValidationResult()
    {
        return $this->_blValidationResult;
    }

    /**
     * Indicates whether the exception was thrown
     *
     * @return bool
     */
    public function isException()
    {
        return $this->_blIsException;
    }

    /**
     * The Online check is performed. Returns check result
     *
     * @param string $sSerial Serial key to be checked
     *
     * @return bool
     */
    public function validate($sSerial = null)
    {
        if ( is_null( $sSerial ) ) {
            $oConfig = oxRegistry::getConfig();
            $sSerial = $oConfig->getConfigParam("aSerials");
        }
        $aSerial = (array) $sSerial;
        $this->_setIsException(false);

        $blResult = false;
        try {
            $oRequest = $this->_formRequest($aSerial);

            $oCaller = $this->_getCaller();
            $oResponse = $oCaller->doRequest($oRequest, 'OLC');

            $blResult = $this->_validateResponse($oResponse);

            if ($blResult) {
                $this->_logSuccess();
            }
        } catch (oxException $oEx) {
            $this->_setErrorMessage($oEx->getMessage());
            $this->_setIsException(true);
        }

        return $blResult;
    }

    /**
     * Set error message.
     *
     * @param $sErrorMessage string
     */
    protected function _setErrorMessage($sErrorMessage)
    {
        $this->_sErrorMessage = $sErrorMessage;
    }

    /**
     * @return oxOnlineLicenseCheckCaller
     */
    protected function _getCaller()
    {
        return $this->_oCaller;
    }

    /**
     * Performs a check of the response code and message.
     *
     * @param $oResponse
     * @throws oxException
     *
     * @return bool
     */
    protected function _validateResponse($oResponse)
    {
        if (isset($oResponse->code) && isset($oResponse->message)) {
            if ($oResponse->code == $this->_iValidResponseCode &&
                $oResponse->message == $this->_sValidResponseMessage
            ) {
                // serial keys are valid
                $this->_blValidationResult = true;
            } else {
                // serial keys are not valid
                $this->_setErrorMessage(oxRegistry::getLang()->translateString('OLC_ERROR_SERIAL_NOT_VALID'));
                $this->_blValidationResult = false;
            }
        } else {
            // validation result is unknown
            throw new oxException('OLC_ERROR_RESPONSE_NOT_VALID');
        }

        return $this->_blValidationResult;
    }

    /**
     * Builds request object with required parameters.
     *
     * @param array $aSerial
     * @throws oxException
     *
     * @return oxOnlineLicenseCheckRequest
     */
    protected function _formRequest($aSerial)
    {
        $oConfig = oxRegistry::getConfig();

        /** @var oxOnlineLicenseCheckRequest $oRequest */
        $oRequest = oxNew('oxOnlineLicenseCheckRequest');

        $oRequest->revision = $oConfig->getRevision();

        $oRequest->keys = new stdClass();
        $oRequest->keys->key = $aSerial;

        $oRequest->servers = new stdClass();
        $oRequest->servers->server = $oConfig->getConfigParam('aServersData');

        return $oRequest;
    }

    /**
     * Registers the latest Successful Online License check
     */
    protected function _logSuccess()
    {
        $iTime = oxRegistry::get("oxUtilsDate")->getTime();
        $sBaseShop = oxRegistry::getConfig()->getBaseShopId();
        oxRegistry::getConfig()->saveShopConfVar("str", oxOnlineLicenseCheck::CONFIG_VAR_NAME, $iTime, $sBaseShop);
    }

    /**
     * Sets exception flag
     *
     * @param bool $blIsException Exception flag
     */
    protected function _setIsException($blIsException)
    {
        $this->_blIsException = $blIsException;
    }
}