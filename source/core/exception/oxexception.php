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
 * Basic exception class
 *
 */
class oxException extends Exception
{
    /**
     * Log file path/name
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
     * @param string  $sMessage exception message
     * @param integer $iCode    exception code
     */
    public function __construct($sMessage = "not set", $iCode = 0)
    {
        parent::__construct($sMessage, $iCode);
    }

    /**
     * Set log file path/name
     *
     * @param string $sFile File name
     *
     * @return null
     */
    public function setLogFileName($sFile)
    {
        $this->_sFileName = $sFile;
    }

    /**
     * Get log file path/name
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
     *
     * @return null
     */
    public function setMessage($sMessage)
    {
        $this->message = $sMessage;
    }

    /**
     * To define that the exception was caught in renderer
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
     */
    public function debugOut()
    {
        //We are most likely are already dealing with an exception so making sure no other exceptions interfere
        try {
            $sLogMsg = $this->getString() . "\n---------------------------------------------\n";
            oxRegistry::getUtils()->writeToLog( $sLogMsg, $this->getLogFileName() );
        } catch (Exception $e) {
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
        if ( $this->_blNotCaught ) {
            $sWarning .= "--!--NOT CAUGHT--!--";
        }

        if ( $this->_blRenderer ) {
            $sWarning .= "--!--RENDERER--!--";
        }

        return $sWarning . __CLASS__ . " (time: ". date('Y-m-d H:i:s') ."): [{$this->code}]: {$this->message} \n Stack Trace: {$this->getTraceAsString()}\n\n";
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
     *
     * @return null
     */
    public function setDestination( $sDestination )
    {
    }
}
