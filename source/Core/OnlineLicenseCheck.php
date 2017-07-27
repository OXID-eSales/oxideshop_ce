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

use stdClass;
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
    protected $validResponseCode = 0;

    /**
     * Expected valid response message.
     *
     * @var string
     */
    protected $validResponseMessage = 'ACK';

    /**
     * List of serial keys to validate.
     *
     * @var array
     */
    protected $serialKeys = array();

    /**
     * Error message for the user.
     *
     * @var string
     */
    protected $errorMessage = '';

    /**
     * Indicates exception event
     *
     * @var bool
     */
    protected $isException = false;

    /**
     * @var \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller
     */
    protected $caller = null;

    /**
     * @var \OxidEsales\Eshop\Core\UserCounter
     */
    protected $userCounter = null;

    /**
     * @var \OxidEsales\Eshop\Core\Service\ApplicationServerExporterInterface
     */
    protected $appServerExporter = null;

    /**
     * Sets servers manager.
     *
     * @param \OxidEsales\Eshop\Core\Service\ApplicationServerExporterInterface $appServerExporter
     */
    public function setAppServerExporter($appServerExporter)
    {
        $this->appServerExporter = $appServerExporter;
    }

    /**
     * Gets servers manager.
     *
     * @return \OxidEsales\Eshop\Core\Service\ApplicationServerExporterInterface
     */
    public function getAppServerExporter()
    {
        return $this->appServerExporter;
    }

    /**
     * Sets user counter.
     *
     * @param \OxidEsales\Eshop\Core\UserCounter $userCounter
     */
    public function setUserCounter($userCounter)
    {
        $this->userCounter = $userCounter;
    }

    /**
     * Gets user counter.
     *
     * @return \OxidEsales\Eshop\Core\UserCounter
     */
    protected function getUserCounter()
    {
        return $this->userCounter;
    }

    /**
     * Sets dependencies.
     *
     * @param \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller $caller
     */
    public function __construct($caller)
    {
        $this->caller = $caller;
    }

    /**
     * Get error message.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Indicates whether the exception was thrown
     *
     * @return bool
     */
    public function isException()
    {
        return $this->isException;
    }

    /**
     * Takes active serial key and performs online license check in case it has never been performed before.
     * In case of invalid license key, eShop is declared as unlicensed.
     * In case of validation exception (eg. service can not be reached) the check is postponed until the next call.
     */
    public function validateShopSerials()
    {
        $aSerials = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam("aSerials");
        if (!$this->validate($aSerials) && !$this->isException()) {
            $this->startGracePeriod();
        }
    }

    /**
     * The Online shop license check for the new serial is performed. Returns check result.
     *
     * @param string $serial Serial to check.
     *
     * @return bool
     */
    public function validateNewSerial($serial)
    {
        $serials = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam("aSerials");
        $serials[] = array('attributes' => array('state' => 'new'), 'value' => $serial);

        return $this->validate($serials);
    }

    /**
     * The Online shop license check is performed. Returns check result.
     *
     * @param array $serials Serial keys to be checked.
     *
     * @return bool
     */
    public function validate($serials)
    {
        $serials = (array)$serials;
        $this->setIsException(false);

        $result = false;
        try {
            $request = $this->formRequest($serials);

            $caller = $this->getCaller();
            $response = $caller->doRequest($request);

            $result = $this->validateResponse($response);

            if ($result) {
                $this->logSuccess();
            }
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $ex) {
            $this->setErrorMessage($ex->getMessage());
            $this->setIsException(true);
        }

        return $result;
    }

    /**
     * Set error message.
     *
     * @param string $errorMessage Error message
     */
    protected function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Gets caller.
     *
     * @return \OxidEsales\Eshop\Core\OnlineLicenseCheckCaller
     */
    protected function getCaller()
    {
        return $this->caller;
    }

    /**
     * Performs a check of the response code and message.
     *
     * @param \OxidEsales\Eshop\Core\OnlineLicenseCheckResponse $response
     *
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     *
     * @return bool
     */
    protected function validateResponse($response)
    {
        if (isset($response->code) && isset($response->message)) {
            if ($response->code == $this->validResponseCode &&
                $response->message == $this->validResponseMessage
            ) {
                // serial keys are valid
                $valid = true;
            } else {
                // serial keys are not valid
                $this->setErrorMessage(\OxidEsales\Eshop\Core\Registry::getLang()->translateString('OLC_ERROR_SERIAL_NOT_VALID'));
                $valid = false;
            }
        } else {
            // validation result is unknown
            throw new \OxidEsales\Eshop\Core\Exception\StandardException('OLC_ERROR_RESPONSE_NOT_VALID');
        }

        return $valid;
    }

    /**
     * Builds request object with required parameters.
     *
     * @param array $serials Array of serials to add to request.
     *
     * @throws oxException
     *
     * @return \OxidEsales\Eshop\Core\OnlineLicenseCheckRequest
     */
    protected function formRequest($serials)
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        /** @var \OxidEsales\Eshop\Core\OnlineLicenseCheckRequest $request */
        $request = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckRequest::class);

        $request->revision = $config->getRevision();

        $request->keys = array('key' => $serials);

        $request->productSpecificInformation = new stdClass();

        if (!is_null($this->getAppServerExporter())) {
            $servers = $this->getAppServerExporter()->exportAppServerList();
            $request->productSpecificInformation->servers = array('server' => $servers);
        }

        $counters = $this->formCounters();
        if (!empty($counters)) {
            $request->productSpecificInformation->counters = array('counter' => $counters);
        }

        return $request;
    }

    /**
     * Forms shop counters array for sending to OXID server.
     *
     * @return array
     */
    protected function formCounters()
    {
        $userCounter = $this->getUserCounter();

        $counters = array();

        if (!is_null($this->getUserCounter())) {
            $counters[] = array(
                'name' => 'admin users',
                'value' => $userCounter->getAdminCount(),
            );
            $counters[] = array(
                'name' => 'active admin users',
                'value' => $userCounter->getActiveAdminCount(),
            );
        }

        $counters[] = array(
            'name' => 'subShops',
            'value' => \OxidEsales\Eshop\Core\Registry::getConfig()->getMandateCount(),
        );

        return $counters;
    }

    /**
     * Registers the latest Successful Online License check.
     */
    protected function logSuccess()
    {
        $time = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $baseShop = \OxidEsales\Eshop\Core\Registry::getConfig()->getBaseShopId();
        \OxidEsales\Eshop\Core\Registry::getConfig()->saveShopConfVar(
            "str",
            \OxidEsales\Eshop\Core\OnlineLicenseCheck::CONFIG_VAR_NAME,
            $time,
            $baseShop
        );
    }

    /**
     * Sets exception flag.
     *
     * @param bool $isException Exception flag.
     */
    protected function setIsException($isException)
    {
        $this->isException = $isException;
    }

    /**
     * Starts grace period.
     * Sets to config options.
     */
    protected function startGracePeriod()
    {
    }
}
