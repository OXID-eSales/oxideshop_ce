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
 * e.g.:
 * - not existing object
 * - wrong type
 * - ID not set
 */
class oxObjectException extends oxException
{

    /**
     * Object causing exception.
     *
     * @var object
     */
    private $_oObject;

    /**
     * Sets the object which caused the exception.
     *
     * @param object $oObject exception object
     */
    public function setObject($oObject)
    {
        $this->_oObject = $oObject;
    }

    /**
     * Get the object which caused the exception.
     *
     * @return object
     */
    public function getObject()
    {
        return $this->_oObject;
    }

    /**
     * Get string dump
     * Overrides oxException::getString()
     *
     * @return string
     */
    public function getString()
    {
        return __CLASS__ . '-' . parent::getString() . " Faulty Object --> " . get_class($this->_oObject) . "\n";
    }
}
