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
 * exceptions for missing components e.g.:
 * - missing class
 * - missing function
 * - missing template
 * - missing field in object
 */
class oxSystemComponentException extends oxException
{
    /**
     * Component causing the exception.
     *
     * @var string
     */
    private $_sComponent;

    /**
     * Sets the component name which caused the exception as a string.
     *
     * @param string $sComponent name of component
     *
     * @return null
     */
    public function setComponent( $sComponent )
    {
        $this->_sComponent = $sComponent;
    }

    /**
     * Name of the component that caused the exception
     *
     * @return string
     */
    public function getComponent()
    {
        return $this->_sComponent;
    }

    /**
     * Get string dump
     * Overrides oxException::getString()
     *
     * @return string
     */
    public function getString()
    {
        return __CLASS__.'-'.parent::getString()." Faulty component --> ".$this->_sComponent;
    }

    /**
     * Creates an array of field name => field value of the object.
     * To make a easy conversion of exceptions to error messages possible.
     * Should be extended when additional fields are used!
     * Overrides oxException::getValues().
     *
     * @return array
     */
    public function getValues()
    {
        $aRes = parent::getValues();
        $aRes['component'] = $this->getComponent();
        return $aRes;
    }
}
