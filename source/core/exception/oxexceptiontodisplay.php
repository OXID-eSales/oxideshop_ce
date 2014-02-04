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
 * simplified Exception classes for simply displaying errors
 * saves resources when exception functionality is not needed
 */
class oxExceptionToDisplay implements oxIDisplayError
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
     *
     * @return null
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
     * Sets oxExceptionToDisplay::_aValues value
     *
     * @param array $aValues exception values to store
     *
     * @return null
     */
    public function setValues( $aValues )
    {
        $this->_aValues = $aValues;
    }

    /**
     * Stores into exception storage message or other value
     *
     * @param string $sName  storage name
     * @param mixed  $sValue value to store
     *
     * @return null
     */
    public function addValue( $sName, $sValue )
    {
        $this->_aValues[$sName] = $sValue;
    }

    /**
     * Exception type setter
     *
     * @param string $sType exception type
     *
     * @return null
     */
    public function setExceptionType( $sType )
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
    public function getValue( $sName )
    {
        return $this->_aValues[$sName];
    }

    /**
     * Exception debug mode setter
     *
     * @param bool $bl if TRUE debug mode on
     *
     * @return null
     */
    public function setDebug( $bl )
    {
        $this->_blDebug = $bl;
    }

    /**
     * Exception message setter
     *
     * @param string $sMessage exception message
     *
     * @return null
     */
    public function setMessage($sMessage)
    {
        $this->_sMessage = $sMessage;
    }

    /**
     * Sets the exception message arguments used when
     * outputing message using sprintf().
     *
     * @return null
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
        if ( $this->_blDebug ) {
            return $this;
        } else {
             $sString = oxRegistry::getLang()->translateString($this->_sMessage);

             if ( !empty( $this->_aMessageArgs ) ) {
                 $sString = vsprintf( $sString, $this->_aMessageArgs );
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
        $sRes = $this->getErrorClassType() . " (time: " . date('Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime()) . "): " . $this->getOxMessage() . " \n Stack Trace: " . $this->getStackTrace() . "\n";
        foreach ( $this->_aValues as $key => $value ) {
            $sRes .= $key. " => ". $value . "\n";
        }
        return $sRes;
    }
}
