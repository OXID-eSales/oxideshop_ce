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
 * exception class covering voucher exceptions
 */
class oxVoucherException extends oxException
{
    /**
     * Voucher nr. involved in this exception
     *
     * @var string
     */
    private $_sVoucherNr;

    /**
     * Sets the voucher number as a string
     *
     * @param string $sVoucherNr voucher number
     *
     * @return null
     */
    public function setVoucherNr( $sVoucherNr )
    {
        $this->_sVoucherNr = ( string ) $sVoucherNr;
    }

    /**
     * get voucher nr. involved
     *
     * @return string
     */
    public function getVoucherNr()
    {
        return $this->_sVoucherNr;
    }

    /**
     * Get string dump
     * Overrides oxException::getString()
     *
     * @return string
     */
    public function getString()
    {
        return __CLASS__.'-'.parent::getString()." Faulty Voucher Nr --> ".$this->_sVoucherNr;
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
        $aRes['voucherNr'] = $this->getVoucherNr();
        return $aRes;
    }
}
