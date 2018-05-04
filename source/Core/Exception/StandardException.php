<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

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
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
     *
     * @var string
     */
    protected $_sFileName = OX_LOG_FILE;

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
     * @param string          $sMessage exception message
     * @param integer         $iCode    exception code
     * @param \Exception|null $previous previous exception
     */
    public function __construct($sMessage = "not set", $iCode = 0, \Exception $previous = null)
    {
        parent::__construct($sMessage, $iCode, $previous);
    }

    /**
     * Set log file name. The file will always be relative to the directory of OX_LOG_FILE
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
     *
     * @param string $fileName File name
     */
    public function setLogFileName($fileName)
    {
        $fileName = dirname(OX_LOG_FILE) . DIRECTORY_SEPARATOR . basename($fileName);

        $this->_sFileName = $fileName;
    }

    /**
     * Get log file path/name
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
     *
     * @return string
     */
    public function getLogFileName()
    {
        return basename($this->_sFileName);
    }

    /**
     * Sets the exception message
     *
     *  @deprecated since v6.0 (2017-02-27); This method will be removed. Set message in the constructor.
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
     * Write exception to log file
     *
     * @deprecated since v6.3 (2018-04-25); This method will be removed completely. Use Registry::getLogger() to log error messages in the future.
     *
     * @return mixed
     */
    public function debugOut()
    {
        $exceptionHandler = new \OxidEsales\Eshop\Core\Exception\ExceptionHandler();
        /**
         * @deprecated since v6.0 (2017-02-27); Logging mechanism will be changed in 6.0.
         */
        $exceptionHandler->setLogFileName($this->getLogFileName());

        return $exceptionHandler->writeExceptionToLog($this);
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

        $currentTime = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());

        return $sWarning . __CLASS__ . " (time: " . $currentTime . "): [{$this->code}]: {$this->message} \n Stack Trace: {$this->getTraceAsString()}\n\n";
    }

    /**
     * Creates an array of field name => field value of the object.
     * To make a easy conversion of exceptions to error messages possible.
     * Should be extended when additional fields are used!
     *
     * @return array
     */
    public function getValues()
    {
        return [];
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
