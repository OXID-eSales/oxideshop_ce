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

/**
 * Basic exception class
 *
 */
class StandardException extends \Exception
{
    /**
     * Exception type, currently old class name is used.
     *
     * @var string
     */
    protected $type = 'oxException';

    /**
     * Log file path/name
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will be changed in 6.0.
     *
     * @var string
     */
    protected $_sFileName = 'EXCEPTION_LOG.txt';

    /**
     * Not caught means the exception was not caught and occured in the rendering process,
     * which is not allowed!
     *
     * @var bool
     */
    protected $_blRenderer = false;

    /**
     * Indicates that the Exception was caught in oxshopcontrol, which should be avoided!
     *
     * @var bool
     */
    protected $_blNotCaught = false;

    /**
     * Default constructor
     *
     * @param string            $sMessage exception message
     * @param integer           $iCode    exception code
     * @param Exception|null    $previous previous exception
     */    
    public function __construct($sMessage = "not set", $iCode = 0, \Exception $previous = null)
    {
        parent::__construct($sMessage, $iCode, $previous);
    }

    /**
     * Set log file path/name
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will be changed in 6.0.
     *
     * @param string $sFile File name
     */
    public function setLogFileName($sFile)
    {
        $this->_sFileName = $sFile;
    }

    /**
     * Get log file path/name
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will be changed in 6.0.
     *
     * @return string
     */
    public function getLogFileName()
    {
        return $this->_sFileName;
    }

    /**
     * Sets the exception message
     *
     * @param string $sMessage exception message
     */
    public function setMessage($sMessage)
    {
        $this->message = $sMessage;
    }

    /**
     * To define that the exception was caught in renderer
     */
    public function setRenderer()
    {
        $this->_blRenderer = true;
    }

    /**
     * Is the exception caught in a renderer
     *
     * @return bool
     */
    public function isRenderer()
    {
        return $this->_blRenderer;
    }

    /**
     * To define that the exception was not caught (only in oxexceptionhandler)
     */
    public function setNotCaught()
    {
        $this->_blNotCaught = true;
    }

    /**
     * Is the exception "not" caught.
     *
     * @return bool
     */
    public function isNotCaught()
    {
        return $this->_blNotCaught;
    }

    /**
     * Prints exception in file EXCEPTION_LOG.txt
     */
    public function debugOut()
    {
        //We are most likely are already dealing with an exception so making sure no other exceptions interfere
        try {
            $sLogMsg = $this->getString() . "\n---------------------------------------------\n";
            //deprecated since v5.3 (2016-06-17); Logging mechanism will be changed in 6.0.
            oxRegistry::getUtils()->writeToLog($sLogMsg, $this->getLogFileName());
            //end deprecated
        } catch (\Exception $e) {
        }
    }

    /**
     * Get complete string dump, should be overwritten by excptions extending this exceptions
     * if they introduce new fields
     *
     * @return string
     */
    public function getString()
    {
        $sWarning = "";
        if ($this->_blNotCaught) {
            $sWarning .= "--!--NOT CAUGHT--!--";
        }

        if ($this->_blRenderer) {
            $sWarning .= "--!--RENDERER--!--";
        }

        $currentTime = date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime());

        return $sWarning . __CLASS__ . " (time: " . $currentTime . "): [{$this->code}]: {$this->message} \n Stack Trace: {$this->getTraceAsString()}\n\n";
    }

    /**
     * Default __toString method wraps getString(). In the shop no __toString() is used to be PHP 5.1 compatible,
     *
     * @return string
     */
    /*
    public function __toString()
    {
        return $this->getString();
    }
    */
    /**
     * Creates an array of field name => field value of the object.
     * To make a easy conversion of exceptions to error messages possible.
     * Should be extended when additional fields are used!
     *
     * @return array
     */
    public function getValues()
    {
        return array();
    }

    /**
     * Defines a name of the view variable containing the messages
     *
     * @param string $sDestination name of the view variable
     */
    public function setDestination($sDestination)
    {
    }

    /**
     * Get exception type.
     * Currently old class name is used here for compatibility.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
