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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core;

use oxOnlineLicenseCheckCaller;
use oxUserCounter;
use oxServersManager;
use oxRegistry;
use oxOnlineLicenseCheck;
use stdClass;
use oxOnlineLicenseCheckRequest;
use oxException;

/**
 * Performs Online License Key check.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class OnlineLicenseCheck
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

    /** @var oxOnlineLicenseCheckCaller */
    protected $_oCaller = null;

    /** @var oxUserCounter */
    protected $_oUserCounter = null;

    /** @var oxServersManager */
    protected $_oServersManager = null;

    /**
     * Sets servers manager.
     *
     * @param oxServersManager $oServersManager
     */
    public function setServersManager($oServersManager)
    {
        $this->_oServersManager = $oServersManager;
    }

    /**
     * Gets servers manager.
     *
     * @return oxServersManager
     */
    public function getServersManager()
    {
        return $this->_oServersManager;
    }

    /**
     * Sets user counter.
     *
     * @param oxUserCounter $oUserCounter
     */
    public function setUserCounter($oUserCounter)
    {
        $this->_oUserCounter = $oUserCounter;
    }

    /**
     * Gets user counter.
     *
     * @return oxUserCounter
     */
    public function getUserCounter()
    {
        return $this->_oUserCounter;
    }


    /**
     * Sets dependencies.
     *
     * @param oxOnlineLicenseCheckCaller $oCaller
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
     * Indicates whether the exception was thrown
     *
     * @return bool
     */
    public function isException()
    {
        return $this->_blIsException;
    }

    /**
     * Takes active serial key and performs online license check in case it has never been performed before.
     * In case of invalid license key, eShop is declared as unlicensed.
     * In case of validation exception (eg. service can not be reached) the check is postponed until the next call.
     */
    public function validateShopSerials()
    {
        $aSerials = oxRegistry::getConfig()->getConfigParam("aSerials");
        if (!$this->validate($aSerials) && !$this->isException()) {
            $this->_startGracePeriod();
        }
    }

    /**
     * The Online shop license check for the new serial is performed. Returns check result.
     *
     * @param string $sSerial Serial to check.
     *
     * @return bool
     */
    public function validateNewSerial($sSerial)
    {
        $aSerials = oxRegistry::getConfig()->getConfigParam("aSerials");
        $aSerials[] = array('attributes' => array('state' => 'new'), 'value' => $sSerial);

        return $this->validate($aSerials);
    }

    /**
     * The Online shop license check is performed. Returns check result.
     *
     * @param array $aSerials Serial keys to be checked.
     *
     * @return bool
     */
    public function validate($aSerials)
    {
        $aSerials = (array)$aSerials;
        $this->_setIsException(false);

        $blResult = false;
        try {
            $oRequest = $this->_formRequest($aSerials);

            $oCaller = $this->_getCaller();
            $oResponse = $oCaller->doRequest($oRequest);

            $blResult = $this->_validateResponse($oResponse);

            if ($blResult) {
                $this->_logSuccess();
            }
        } catch (\OxidEsales\EshopCommunity\Core\Exception\StandardException $oEx) {
            $this->_setErrorMessage($oEx->getMessage());
            $this->_setIsException(true);
        }

        return $blResult;
    }

    /**
     * Set error message.
     *
     * @param string $sErrorMessage Error message
     */
    protected function _setErrorMessage($sErrorMessage)
    {
        $this->_sErrorMessage = $sErrorMessage;
    }

    /**
     * Gets caller.
     *
     * @return oxOnlineLicenseCheckCaller
     */
    protected function _getCaller()
    {
        return $this->_oCaller;
    }

    /**
     * Performs a check of the response code and message.
     *
     * @param oxOnlineLicenseCheckResponse $oResponse
     *
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
                $blValid = true;
            } else {
                // serial keys are not valid
                $this->_setErrorMessage(oxRegistry::getLang()->translateString('OLC_ERROR_SERIAL_NOT_VALID'));
                $blValid = false;
            }
        } else {
            // validation result is unknown
            throw new oxException('OLC_ERROR_RESPONSE_NOT_VALID');
        }

        return $blValid;
    }

    /**
     * Builds request object with required parameters.
     *
     * @param array $aSerials Array of serials to add to request.
     *
     * @throws oxException
     *
     * @return oxOnlineLicenseCheckRequest
     */
    protected function _formRequest($aSerials)
    {
        $oConfig = oxRegistry::getConfig();

        /** @var oxOnlineLicenseCheckRequest $oRequest */
        $oRequest = oxNew('oxOnlineLicenseCheckRequest');

        $oRequest->revision = $oConfig->getRevision();

        $oRequest->keys = array('key' => $aSerials);

        $oRequest->productSpecificInformation = new stdClass();

        if (!is_null($this->getServersManager())) {
            $aServers = $this->getServersManager()->getServers();
            $oRequest->productSpecificInformation->servers = array('server' => $aServers);
        }

        $aCounters = $this->_formCounters();
        if (!empty($aCounters)) {
            $oRequest->productSpecificInformation->counters = array('counter' => $aCounters);
        }

        return $oRequest;
    }

    /**
     * Forms shop counters array for sending to OXID server.
     *
     * @return array
     */
    protected function _formCounters()
    {
        $oUserCounter = $this->_getUserCounter();

        $aCounters = array();

        if (!is_null($this->getUserCounter())) {
            $aCounters[] = array(
                'name' => 'admin users',
                'value' => $oUserCounter->getAdminCount(),
            );
            $aCounters[] = array(
                'name' => 'active admin users',
                'value' => $oUserCounter->getActiveAdminCount(),
            );
        }

        $aCounters[] = array(
            'name' => 'subShops',
            'value' => oxRegistry::getConfig()->getMandateCount(),
        );

        return $aCounters;
    }

    /**
     * Registers the latest Successful Online License check.
     */
    protected function _logSuccess()
    {
        $iTime = oxRegistry::get("oxUtilsDate")->getTime();
        $sBaseShop = oxRegistry::getConfig()->getBaseShopId();
        oxRegistry::getConfig()->saveShopConfVar("str", oxOnlineLicenseCheck::CONFIG_VAR_NAME, $iTime, $sBaseShop);
    }

    /**
     * Sets exception flag.
     *
     * @param bool $blIsException Exception flag.
     */
    protected function _setIsException($blIsException)
    {
        $this->_blIsException = $blIsException;
    }

    /**
     * Starts grace period.
     * Sets to config options.
     */
    protected function _startGracePeriod()
    {
    }

    /**
     * Gets user counter.
     *
     * @return oxUserCounter
     */
    protected function _getUserCounter()
    {
        return $this->_oUserCounter;
    }
}
