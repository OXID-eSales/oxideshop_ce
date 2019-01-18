<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * simplified Exception classes for simply displaying errors
 * saves resources when exception functionality is not needed
 */
class ExceptionToDisplay implements \OxidEsales\Eshop\Core\Contract\IDisplayError
{
    /**
     * Language const of a Message
     *
     * @var string
     */
    private $_sMessage;

    /**
     * Shop debug
     *
     * @var integer
     */
    protected $_blDebug = false;

    /**
     * Stack trace as a string
     *
     * @var string
     */
    private $_sStackTrace;

    /**
     * Additional values
     *
     * @var string
     */
    private $_aValues;

    /**
     * Typeof the exception (old class name)
     *
     * @var string
     */
    private $_sType;

    /**
     * Stack trace setter
     *
     * @param string $sStackTrace stack trace
     */
    public function setStackTrace($sStackTrace)
    {
        $this->_sStackTrace = $sStackTrace;
    }

    /**
     * Returns stack trace
     *
     * @return string
     */
    public function getStackTrace()
    {
        return $this->_sStackTrace;
    }

    /**
     * Sets \OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::_aValues value
     *
     * @param array $aValues exception values to store
     */
    public function setValues($aValues)
    {
        $this->_aValues = $aValues;
    }

    /**
     * Stores into exception storage message or other value
     *
     * @param string $sName  storage name
     * @param mixed  $sValue value to store
     */
    public function addValue($sName, $sValue)
    {
        $this->_aValues[$sName] = $sValue;
    }

    /**
     * Exception type setter
     *
     * @param string $sType exception type
     */
    public function setExceptionType($sType)
    {
        $this->_sType = $sType;
    }

    /**
     * Returns error class type
     *
     * @return string
     */
    public function getErrorClassType()
    {
        return $this->_sType;
    }

    /**
     * Returns exception stored (by name) value
     *
     * @param string $sName storage name
     *
     * @return  mixed
     */
    public function getValue($sName)
    {
        return $this->_aValues[$sName];
    }
    
    /**
     * Returns all exception stored values
     *
     * @return  array
     */
    public function getValues()
    {
        return $this->_aValues;
    }
    
    /**
     * Exception debug mode setter
     *
     * @param bool $bl if TRUE debug mode on
     */
    public function setDebug($bl)
    {
        $this->_blDebug = $bl;
    }

    /**
     * Exception message setter
     *
     * @param string $sMessage exception message
     */
    public function setMessage($sMessage)
    {
        $this->_sMessage = $sMessage;
    }

    /**
     * Sets the exception message arguments used when
     * outputing message using sprintf().
     */
    public function setMessageArgs()
    {
        $this->_aMessageArgs = func_get_args();
    }

    /**
     * Returns translated exception message
     *
     * @return string
     */
    public function getOxMessage()
    {
        if ($this->_blDebug) {
            return $this;
        } else {
            $sString = \OxidEsales\Eshop\Core\Registry::getLang()->translateString($this->_sMessage);

            if (!empty($this->_aMessageArgs)) {
                $sString = vsprintf($sString, $this->_aMessageArgs);
            }

            return $sString;
        }
    }

    /**
     * When exception is converted as string, this magic method return exception message
     *
     * @return string
     */
    public function __toString()
    {
        $sRes = $this->getErrorClassType() . " (time: " . date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()) . "): " . $this->getOxMessage() . " \n Stack Trace: " . $this->getStackTrace() . "\n";
        foreach ($this->_aValues as $key => $value) {
            $sRes .= $key . " => " . $value . "\n";
        }

        return $sRes;
    }
}
