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

namespace OxidEsales\Eshop\Core\Exception;

use oxRegistry;
use Exception;
use oxException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Exception handler, deals with all high level exceptions (caught in oxShopControl)
 */
class ExceptionHandler implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * Shop debug
     *
     * @var integer
     */
    protected $_iDebug = 0;

    /**
     * Class constructor
     *
     * @param integer $iDebug debug level
     */
    public function __construct($iDebug = 0)
    {
        $this->_iDebug = (int) $iDebug;
    }

    /**
     * Set the debug level
     *
     * @param int $iDebug debug level (0== no debug)
     */
    public function setIDebug($iDebug)
    {
        $this->_iDebug = $iDebug;
    }


    /**
     * Uncaught exception handler, deals with uncaught exceptions (global)
     *
     * @param Exception $oEx exception object
     *
     * @return null
     */
    public function handleUncaughtException($oEx)
    {
        // split between php or shop exception
        if (!($oEx instanceof \OxidEsales\Eshop\Core\Exception\StandardException)) {
            $this->_dealWithNoOxException($oEx);

            return; // Return straight away ! (in case of unit testing)
        }
        
        $this->_uncaughtException($oEx); // Return straight away ! (in case of unit testing)
    }

    /**
     * Deal with uncaught oxException exceptions.
     *
     * @param oxException $oEx Exception to handle
     *
     * @return null
     */
    protected function _uncaughtException($oEx)
    {
        // exception occurred in function processing
        $oEx->setNotCaught();
        // general log entry for all exceptions here
        $oEx->debugOut();

        if (0 != $this->_iDebug) {
            oxRegistry::getUtils()->showMessageAndExit($oEx->getString());
        } else {
            oxRegistry::getUtils()->redirectOffline(500);
        }
    }

    /**
     * No oxException, just write log file.
     *
     * @param Exception $oEx exception object
     *
     * @return null
     */
    protected function _dealWithNoOxException($oEx)
    {
        $sLogMsg = date('Y-m-d H:i:s') . $oEx . "\n---------------------------------------------\n";
        $this->logger->error($sLogMsg);

        if (0 != $this->_iDebug) {
            oxRegistry::getUtils()->showMessageAndExit($sLogMsg);
        } else {
           oxRegistry::getUtils()->redirectOffline(500);
        }
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $sMethod Methods name
     * @param array  $aArgs   Argument array
     *
     * @throws \oxSystemComponentException Throws an exception if the called method does not exist or is not accessible in current class
     *
     * @return string
     */
    public function __call($sMethod, $aArgs)
    {
        if (defined('OXID_PHP_UNIT')) {
            if (substr($sMethod, 0, 4) == "UNIT") {
                $sMethod = str_replace("UNIT", "_", $sMethod);
            }
            if (method_exists($this, $sMethod)) {
                return call_user_func_array(array(& $this, $sMethod), $aArgs);
            }
        }

        throw new \oxSystemComponentException("Function '$sMethod' does not exist or is not accessible! (" . __CLASS__ . ")" . PHP_EOL);
    }
}
