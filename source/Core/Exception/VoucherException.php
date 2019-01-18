<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

/**
 * exception class covering voucher exceptions
 */
class VoucherException extends \OxidEsales\Eshop\Core\Exception\StandardException
{
    /**
     * Exception type, currently old class name is used.
     *
     * @var string
     */
    protected $type = 'oxVoucherException';

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
     */
    public function setVoucherNr($sVoucherNr)
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
        return __CLASS__ . '-' . parent::getString() . " Faulty Voucher Nr --> " . $this->_sVoucherNr;
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
