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
 * Exception class for a non existing language local
 */
class oxLanguageException extends oxException
{
    /**
     * Language constant
     *
     * @var string
     */
    private $_sLangConstant = "";

    /**
     * sets the language constant which is missing
     *
     * @param string $sLangConstant language constant
     *
     * @return null
     */
    public function setLangConstant( $sLangConstant )
    {
        $this->_sLangConstant = $sLangConstant;
    }

    /**
    * Get language constant
    *
    * @return string
    */
    public function getLangConstant()
    {
        return $this->_sLangConstant;
    }

    /**
     * Get string dump
     * Overrides oxException::getString()
     *
     * @return string
     */
    public function getString()
    {
        return __CLASS__.'-'.parent::getString()." Faulty Constant --> ".$this->_sLangConstant."\n";
    }

    /**
     * Creates an array of field name => field value of the object
     * to make a easy conversion of exceptions to error messages possible
     * Overrides oxException::getValues()
     * should be extended when additional fields are used!
     *
     * @return array
     */
    public function getValues()
    {
        $aRes = parent::getValues();
        $aRes['langConstant'] = $this->getLangConstant();
        return $aRes;
    }
}
