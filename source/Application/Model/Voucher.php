<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;
use stdClass;
use oxRegistry;
use oxField;

/**
 * Voucher manager.
 * Performs deletion, generating, assigning to group and other voucher
 * managing functions.
 *
 */
class Voucher extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    protected $_oSerie = null;

    /**
     * Vouchers does not need shop id check as this causes problems with
     * inherited vouchers. Voucher validity check is made by oxVoucher::getVoucherByNr()
     *
     * @var bool
     */
    protected $_blDisableShopCheck = true;

    /**
     * @var string Name of current class
     */
    protected $_sClassName = 'oxvoucher';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxvouchers');
    }

    /**
     * Gets voucher from db by given number.
     *
     * @param string $sVoucherNr         Voucher number
     * @param array  $aVouchers          Array of available vouchers (default array())
     * @param bool   $blCheckavalability check if voucher is still reserver od not
     *
     * @throws oxVoucherException exception
     *
     * @return mixed
     */
    public function getVoucherByNr($sVoucherNr, $aVouchers = [], $blCheckavalability = false)
    {
        $oRet = null;
        if (!empty($sVoucherNr)) {
            $sViewName = $this->getViewName();
            $sSeriesViewName = getViewName('oxvoucherseries');
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();

            $sQ = "select {$sViewName}.* from {$sViewName}, {$sSeriesViewName} where
                        {$sSeriesViewName}.oxid = {$sViewName}.oxvoucherserieid and
                        {$sViewName}.oxvouchernr = " . $oDb->quote($sVoucherNr) . " and ";

            if (is_array($aVouchers)) {
                foreach ($aVouchers as $sVoucherId => $sSkipVoucherNr) {
                    $sQ .= "{$sViewName}.oxid != " . $oDb->quote($sVoucherId) . " and ";
                }
            }
            $sQ .= "( {$sViewName}.oxorderid is NULL || {$sViewName}.oxorderid = '' ) ";
            $sQ .= " and ( {$sViewName}.oxdateused is NULL || {$sViewName}.oxdateused = 0 ) ";

            //voucher timeout for 3 hours
            if ($blCheckavalability) {
                $iTime = time() - $this->_getVoucherTimeout();
                $sQ .= " and {$sViewName}.oxreserved < '{$iTime}' ";
            }

            $sQ .= " limit 1 FOR UPDATE";

            if (!($oRet = $this->assignRecord($sQ))) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
                $oEx->setMessage('ERROR_MESSAGE_VOUCHER_NOVOUCHER');
                $oEx->setVoucherNr($sVoucherNr);
                throw $oEx;
            }
        }

        return $oRet;
    }

    /**
     * marks voucher as used
     *
     * @param string $sOrderId  order id
     * @param string $sUserId   user id
     * @param double $dDiscount used discount
     */
    public function markAsUsed($sOrderId, $sUserId, $dDiscount)
    {
        //saving oxreserved field
        if ($this->oxvouchers__oxid->value) {
            $this->oxvouchers__oxorderid->setValue($sOrderId);
            $this->oxvouchers__oxuserid->setValue($sUserId);
            $this->oxvouchers__oxdiscount->setValue($dDiscount);
            $this->oxvouchers__oxdateused->setValue(date("Y-m-d", \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));
            $this->save();
        }
    }

    /**
     * mark voucher as reserved
     */
    public function markAsReserved()
    {
        //saving oxreserved field
        $sVoucherID = $this->oxvouchers__oxid->value;

        if ($sVoucherID) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
            $sQ = "update oxvouchers set oxreserved = :oxreserved where oxid = :oxid";
            $oDb->execute($sQ, [
                ':oxreserved' => time(),
                ':oxid' => $sVoucherID
            ]);
        }
    }

    /**
     * un mark as reserved
     */
    public function unMarkAsReserved()
    {
        //saving oxreserved field
        $sVoucherID = $this->oxvouchers__oxid->value;

        if ($sVoucherID) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = "update oxvouchers set oxreserved = 0 where oxid = :oxid";
            $oDb->execute($sQ, [':oxid' => $sVoucherID]);
        }
    }

    /**
     * Returns the discount value used.
     *
     * @param double $dPrice price to calculate discount on it
     *
     * @throws oxVoucherException exception
     *
     * @return double
     */
    public function getDiscountValue($dPrice)
    {
        if ($this->_isProductVoucher()) {
            return $this->_getProductDiscountValue((double) $dPrice);
        } elseif ($this->_isCategoryVoucher()) {
            return $this->_getCategoryDiscountValue((double) $dPrice);
        } else {
            return $this->_getGenericDiscountValue((double) $dPrice);
        }
    }

    // Checking General Availability
    /**
     * Checks availability without user logged in. Returns array with errors.
     *
     * @param array  $aVouchers array of vouchers
     * @param double $dPrice    current sum (price)
     *
     * @throws oxVoucherException exception
     *
     * @return array
     */
    public function checkVoucherAvailability($aVouchers, $dPrice)
    {
        $this->_isAvailableWithSameSeries($aVouchers);
        $this->_isAvailableWithOtherSeries($aVouchers);
        $this->_isValidDate();
        $this->_isAvailablePrice($dPrice);
        $this->_isNotReserved();

        // returning true - no exception was thrown
        return true;
    }

    /**
     * Performs basket level voucher availability check (no need to check if voucher
     * is reserved or so).
     *
     * @param array  $aVouchers array of vouchers
     * @param double $dPrice    current sum (price)
     *
     * @throws oxVoucherException exception
     *
     * @return array
     */
    public function checkBasketVoucherAvailability($aVouchers, $dPrice)
    {
        $this->_isAvailableWithSameSeries($aVouchers);
        $this->_isAvailableWithOtherSeries($aVouchers);
        $this->_isValidDate();
        $this->_isAvailablePrice($dPrice);

        // returning true - no exception was thrown
        return true;
    }

    /**
     * Checks availability about price. Returns error array.
     *
     * @param double $dPrice base article price
     *
     * @throws oxVoucherException exception
     *
     * @return array
     */
    protected function _isAvailablePrice($dPrice)
    {
        $oSeries = $this->getSerie();
        $oCur = $this->getConfig()->getActShopCurrencyObject();
        if ($oSeries->oxvoucherseries__oxminimumvalue->value && $dPrice < ($oSeries->oxvoucherseries__oxminimumvalue->value * $oCur->rate)) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
            $oEx->setMessage('ERROR_MESSAGE_VOUCHER_INCORRECTPRICE');
            $oEx->setVoucherNr($this->oxvouchers__oxvouchernr->value);
            throw $oEx;
        }

        return true;
    }

    /**
     * Checks if calculation with vouchers of the same series possible. Returns
     * true on success.
     *
     * @param array $aVouchers array of vouchers
     *
     * @throws oxVoucherException exception
     *
     * @return bool
     *
     */
    protected function _isAvailableWithSameSeries($aVouchers)
    {
        if (is_array($aVouchers)) {
            $sId = $this->getId();
            if (isset($aVouchers[$sId])) {
                unset($aVouchers[$sId]);
            }
            $oSeries = $this->getSerie();
            if (!$oSeries->oxvoucherseries__oxallowsameseries->value) {
                foreach ($aVouchers as $voucherId => $voucherNr) {
                    $oVoucher = oxNew(\OxidEsales\Eshop\Application\Model\Voucher::class);
                    $oVoucher->load($voucherId);
                    if ($this->oxvouchers__oxvoucherserieid->value == $oVoucher->oxvouchers__oxvoucherserieid->value) {
                        $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
                        $oEx->setMessage('ERROR_MESSAGE_VOUCHER_NOTALLOWEDSAMESERIES');
                        $oEx->setVoucherNr($this->oxvouchers__oxvouchernr->value);
                        throw $oEx;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Checks if calculation with vouchers from the other series possible.
     * Returns true on success.
     *
     * @param array $aVouchers array of vouchers
     *
     * @throws oxVoucherException exception
     *
     * @return bool
     */
    protected function _isAvailableWithOtherSeries($aVouchers)
    {
        if (is_array($aVouchers) && count($aVouchers)) {
            $oSeries = $this->getSerie();
            $sIds = implode(',', \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray(array_keys($aVouchers)));
            $blAvailable = true;
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            if (!$oSeries->oxvoucherseries__oxallowotherseries->value) {
                // just search for vouchers with different series
                $sSql = "select 1 from oxvouchers where oxvouchers.oxid in ($sIds) and ";
                $sSql .= "oxvouchers.oxvoucherserieid != :notoxvoucherserieid";
                $blAvailable &= !$oDb->getOne($sSql, [
                    ':notoxvoucherserieid' => $this->oxvouchers__oxvoucherserieid->value
                ]);
            } else {
                // search for vouchers with different series and those vouchers do not allow other series
                $sSql = "select 1 from oxvouchers left join oxvoucherseries on oxvouchers.oxvoucherserieid=oxvoucherseries.oxid ";
                $sSql .= "where oxvouchers.oxid in ($sIds) and oxvouchers.oxvoucherserieid != :notoxvoucherserieid ";
                $sSql .= "and not oxvoucherseries.oxallowotherseries";
                $blAvailable &= !$oDb->getOne($sSql, [
                    ':notoxvoucherserieid' => $this->oxvouchers__oxvoucherserieid->value
                ]);
            }
            if (!$blAvailable) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
                $oEx->setMessage('ERROR_MESSAGE_VOUCHER_NOTALLOWEDOTHERSERIES');
                $oEx->setVoucherNr($this->oxvouchers__oxvouchernr->value);
                throw $oEx;
            }
        }

        return true;
    }

    /**
     * Checks if voucher is in valid time period. Returns true on success.
     *
     * @throws oxVoucherException exception
     *
     * @return bool
     */
    protected function _isValidDate()
    {
        $oSeries = $this->getSerie();
        $iTime = time();

        // If date is not set will add day before and day after to check if voucher valid today.
        $iTomorrow = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
        $iYesterday = mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"));

        // Checks if beginning date is set, if not set $iFrom to yesterday so it will be valid.
        $iFrom = ((int) $oSeries->oxvoucherseries__oxbegindate->value) ?
            strtotime($oSeries->oxvoucherseries__oxbegindate->value) : $iYesterday;

        // Checks if end date is set, if no set $iTo to tomorrow so it will be valid.
        $iTo = ((int) $oSeries->oxvoucherseries__oxenddate->value) ?
            strtotime($oSeries->oxvoucherseries__oxenddate->value) : $iTomorrow;

        if ($iFrom < $iTime && $iTo > $iTime) {
            return true;
        }

        $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
        $oEx->setMessage('MESSAGE_COUPON_EXPIRED');
        if ($iFrom > $iTime && $iTo > $iTime) {
            $oEx->setMessage('ERROR_MESSAGE_VOUCHER_NOVOUCHER');
        }
        $oEx->setVoucherNr($this->oxvouchers__oxvouchernr->value);
        throw $oEx;
    }

    /**
     * Checks if voucher is not yet reserved before.
     *
     * @throws oxVoucherException exception
     *
     * @return bool
     */
    protected function _isNotReserved()
    {
        if ($this->oxvouchers__oxreserved->value < time() - $this->_getVoucherTimeout()) {
            return true;
        }

        $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
        $oEx->setMessage('EXCEPTION_VOUCHER_ISRESERVED');
        $oEx->setVoucherNr($this->oxvouchers__oxvouchernr->value);
        throw $oEx;
    }

    // Checking User Availability
    /**
     * Checks availability for the given user. Returns array with errors.
     *
     * @param object $oUser user object
     *
     * @throws oxVoucherException exception
     *
     * @return array
     */
    public function checkUserAvailability($oUser)
    {
        $this->_isAvailableInOtherOrder($oUser);
        $this->_isValidUserGroup($oUser);

        // returning true if no exception was thrown
        return true;
    }

    /**
     * Checks if user already used vouchers from this series and can he use it again.
     *
     * @param object $oUser user object
     *
     * @throws oxVoucherException exception
     *
     * @return boolean
     */
    protected function _isAvailableInOtherOrder($oUser)
    {
        $oSeries = $this->getSerie();
        if (!$oSeries->oxvoucherseries__oxallowuseanother->value) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sSelect = 'select count(*) from ' . $this->getViewName() . ' 
                where oxuserid = :oxuserid and ';
            $sSelect .= 'oxvoucherserieid = :oxvoucherserieid and ';
            $sSelect .= '((oxorderid is not NULL and oxorderid != "") or (oxdateused is not NULL and oxdateused != 0)) ';

            $params = [
                ':oxuserid' => $oUser->oxuser__oxid->value,
                ':oxvoucherserieid' => $this->oxvouchers__oxvoucherserieid->value
            ];

            if ($oDb->getOne($sSelect, $params)) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
                $oEx->setMessage('ERROR_MESSAGE_VOUCHER_NOTALLOWEDSAMESERIES');
                $oEx->setVoucherNr($this->oxvouchers__oxvouchernr->value);
                throw $oEx;
            }
        }

        return true;
    }

    /**
     * Checks if user belongs to the same group as the voucher. Returns true on sucess.
     *
     * @param object $oUser user object
     *
     * @throws oxVoucherException exception
     *
     * @return bool
     */
    protected function _isValidUserGroup($oUser)
    {
        $oVoucherSeries = $this->getSerie();
        $oUserGroups = $oVoucherSeries->setUserGroups();

        if (!$oUserGroups->count()) {
            return true;
        }

        if ($oUser) {
            foreach ($oUserGroups as $oGroup) {
                if ($oUser->inGroup($oGroup->getId())) {
                    return true;
                }
            }
        }

        $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
        $oEx->setMessage('ERROR_MESSAGE_VOUCHER_NOTVALIDUSERGROUP');
        $oEx->setVoucherNr($this->oxvouchers__oxvouchernr->value);
        throw $oEx;
    }

    /**
     * Returns compact voucher object which is used in oxBasket
     *
     * @return stdClass
     */
    public function getSimpleVoucher()
    {
        $oVoucher = new stdClass();
        $oVoucher->sVoucherId = $this->getId();
        $oVoucher->sVoucherNr = null;
        if ($this->oxvouchers__oxvouchernr) {
            $oVoucher->sVoucherNr = $this->oxvouchers__oxvouchernr->value;
        }

        // R. set in oxBasket : $oVoucher->fVoucherdiscount = \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency( $this->oxvouchers__oxdiscount->value );

        return $oVoucher;
    }

    /**
     * create oxVoucherSerie object of this voucher
     *
     * @throws oxObjectException
     *
     * @return oxVoucherSerie
     */
    public function getSerie()
    {
        if ($this->_oSerie !== null) {
            return $this->_oSerie;
        }
        $oSerie = oxNew(\OxidEsales\Eshop\Application\Model\VoucherSerie::class);
        if (!$oSerie->load($this->oxvouchers__oxvoucherserieid->value)) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\ObjectException::class);
        }
        $this->_oSerie = $oSerie;

        return $oSerie;
    }

    /**
     * Returns true if voucher is product specific, otherwise false
     *
     * @return boolean
     */
    protected function _isProductVoucher()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $oSeries = $this->getSerie();
        $sSelect = "select 1 from oxobject2discount 
            where oxdiscountid = :oxdiscountid and oxtype = :oxtype";
        $blOk = ( bool ) $oDb->getOne($sSelect, [
            ':oxdiscountid' => $oSeries->getId(),
            ':oxtype' => 'oxarticles'
        ]);

        return $blOk;
    }

    /**
     * Returns true if voucher is category specific, otherwise false
     *
     * @return boolean
     */
    protected function _isCategoryVoucher()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $oSeries = $this->getSerie();
        $sSelect = "select 1 from oxobject2discount 
            where oxdiscountid = :oxdiscountid and oxtype = :oxtype";
        $blOk = ( bool ) $oDb->getOne($sSelect, [
            ':oxdiscountid' => $oSeries->getId(),
            ':oxtype' => 'oxcategories'
        ]);

        return $blOk;
    }

    /**
     * Returns the discount object created from voucher serie data
     *
     * @return object
     */
    protected function _getSerieDiscount()
    {
        $oSeries = $this->getSerie();
        $oDiscount = oxNew(\OxidEsales\Eshop\Application\Model\Discount::class);

        $oDiscount->setId($oSeries->getId());
        $oDiscount->oxdiscount__oxshopid = new \OxidEsales\Eshop\Core\Field($oSeries->oxvoucherseries__oxshopid->value);
        $oDiscount->oxdiscount__oxactive = new \OxidEsales\Eshop\Core\Field(true);
        $oDiscount->oxdiscount__oxactivefrom = new \OxidEsales\Eshop\Core\Field($oSeries->oxvoucherseries__oxbegindate->value);
        $oDiscount->oxdiscount__oxactiveto = new \OxidEsales\Eshop\Core\Field($oSeries->oxvoucherseries__oxenddate->value);
        $oDiscount->oxdiscount__oxtitle = new \OxidEsales\Eshop\Core\Field($oSeries->oxvoucherseries__oxserienr->value);
        $oDiscount->oxdiscount__oxamount = new \OxidEsales\Eshop\Core\Field(1);
        $oDiscount->oxdiscount__oxamountto = new \OxidEsales\Eshop\Core\Field(MAX_64BIT_INTEGER);
        $oDiscount->oxdiscount__oxprice = new \OxidEsales\Eshop\Core\Field(0);
        $oDiscount->oxdiscount__oxpriceto = new \OxidEsales\Eshop\Core\Field(MAX_64BIT_INTEGER);
        $oDiscount->oxdiscount__oxaddsumtype = new \OxidEsales\Eshop\Core\Field($oSeries->oxvoucherseries__oxdiscounttype->value == 'percent' ? '%' : 'abs');
        $oDiscount->oxdiscount__oxaddsum = new \OxidEsales\Eshop\Core\Field($oSeries->oxvoucherseries__oxdiscount->value);
        $oDiscount->oxdiscount__oxitmartid = new \OxidEsales\Eshop\Core\Field();
        $oDiscount->oxdiscount__oxitmamount = new \OxidEsales\Eshop\Core\Field();
        $oDiscount->oxdiscount__oxitmmultiple = new \OxidEsales\Eshop\Core\Field();

        return $oDiscount;
    }

    /**
     * Returns basket item information array from session or order.
     *
     * @param \OxidEsales\Eshop\Application\Model\Discount $oDiscount discount object
     *
     * @return array
     */
    protected function _getBasketItems($oDiscount = null)
    {
        if ($this->oxvouchers__oxorderid->value) {
            return $this->_getOrderBasketItems($oDiscount);
        } elseif ($this->getSession()->getBasket()) {
            return $this->_getSessionBasketItems($oDiscount);
        } else {
            return [];
        }
    }

    /**
     * Returns basket item information (id,amount,price) array takig item list from order.
     *
     * @param \OxidEsales\Eshop\Application\Model\Discount $oDiscount discount object
     *
     * @return array
     */
    protected function _getOrderBasketItems($oDiscount = null)
    {
        if (is_null($oDiscount)) {
            $oDiscount = $this->_getSerieDiscount();
        }

        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $oOrder->load($this->oxvouchers__oxorderid->value);

        $aItems = [];
        $iCount = 0;

        foreach ($oOrder->getOrderArticles(true) as $oOrderArticle) {
            if (!$oOrderArticle->skipDiscounts() && $oDiscount->isForBasketItem($oOrderArticle)) {
                $aItems[$iCount] = [
                    'oxid'     => $oOrderArticle->getProductId(),
                    'price'    => $oOrderArticle->oxorderarticles__oxbprice->value,
                    'discount' => $oDiscount->getAbsValue($oOrderArticle->oxorderarticles__oxbprice->value),
                    'amount'   => $oOrderArticle->oxorderarticles__oxamount->value,
                ];
                $iCount++;
            }
        }

        return $aItems;
    }

    /**
     * Returns basket item information (id,amount,price) array taking item list from session.
     *
     * @param \OxidEsales\Eshop\Application\Model\Discount $oDiscount discount object
     *
     * @return array
     */
    protected function _getSessionBasketItems($oDiscount = null)
    {
        if (is_null($oDiscount)) {
            $oDiscount = $this->_getSerieDiscount();
        }

        $oBasket = $this->getSession()->getBasket();
        $aItems = [];
        $iCount = 0;

        foreach ($oBasket->getContents() as $oBasketItem) {
            if (!$oBasketItem->isDiscountArticle() && ($oArticle = $oBasketItem->getArticle()) && !$oArticle->skipDiscounts() && $oDiscount->isForBasketItem($oArticle)) {
                $aItems[$iCount] = [
                    'oxid'     => $oArticle->getId(),
                    'price'    => $oArticle->getBasketPrice($oBasketItem->getAmount(), $oBasketItem->getSelList(), $oBasket)->getPrice(),
                    'discount' => $oDiscount->getAbsValue($oArticle->getBasketPrice($oBasketItem->getAmount(), $oBasketItem->getSelList(), $oBasket)->getPrice()),
                    'amount'   => $oBasketItem->getAmount(),
                ];

                $iCount++;
            }
        }

        return $aItems;
    }

    /**
     * Returns the discount value used.
     *
     * @param double $dPrice price to calculate discount on it
     *
     * @throws oxVoucherException exception
     *
     * @deprecated on b-dev (2015-03-31); Use function _getGenericDiscountValue()
     *
     * @return double
     */
    protected function _getGenericDiscoutValue($dPrice)
    {
        return $this->_getGenericDiscountValue($dPrice);
    }

    /**
     * Returns the discount value used.
     *
     * @param double $dPrice price to calculate discount on it
     *
     * @throws oxVoucherException exception
     *
     * @return double
     */
    protected function _getGenericDiscountValue($dPrice)
    {
        $oSeries = $this->getSerie();
        if ($oSeries->oxvoucherseries__oxdiscounttype->value == 'absolute') {
            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $dDiscount = $oSeries->oxvoucherseries__oxdiscount->value * $oCur->rate;
        } else {
            $dDiscount = $oSeries->oxvoucherseries__oxdiscount->value / 100 * $dPrice;
        }

        if ($dDiscount > $dPrice) {
            $dDiscount = $dPrice;
        }

        return $dDiscount;
    }


    /**
     * Return discount value
     *
     * @return double
     */
    public function getDiscount()
    {
        $oSeries = $this->getSerie();

        return $oSeries->oxvoucherseries__oxdiscount->value;
    }

    /**
     * Return discount type
     *
     * @return string
     */
    public function getDiscountType()
    {
        $oSeries = $this->getSerie();

        return $oSeries->oxvoucherseries__oxdiscounttype->value;
    }

    /**
     * Returns the discount value used, if voucher is aplied only for specific products.
     *
     * @param double $dPrice price to calculate discount on it
     *
     * @throws oxVoucherException exception
     *
     * @deprecated on b-dev (2015-03-31); Use function _getProductDiscountValue()
     *
     * @return double
     */
    protected function _getProductDiscoutValue($dPrice)
    {
        return $this->_getProductDiscountValue($dPrice);
    }

    /**
     * Returns the discount value used, if voucher is aplied only for specific products.
     *
     * @param double $dPrice price to calculate discount on it
     *
     * @throws oxVoucherException exception
     *
     * @return double
     */
    protected function _getProductDiscountValue($dPrice)
    {
        $oDiscount = $this->_getSerieDiscount();
        $aBasketItems = $this->_getBasketItems($oDiscount);

        // Basket Item Count and isAdmin check (unble to access property $oOrder->_getOrderBasket()->_blSkipVouchersAvailabilityChecking)
        if (!count($aBasketItems) && !$this->isAdmin()) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
            $oEx->setMessage('ERROR_MESSAGE_VOUCHER_NOVOUCHER');
            $oEx->setVoucherNr($this->oxvouchers__oxvouchernr->value);
            throw $oEx;
        }

        $oSeries = $this->getSerie();

        $oVoucherPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);
        $oDiscountPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);
        $oProductPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);
        $oProductTotal = oxNew(\OxidEsales\Eshop\Core\Price::class);

        // Is the voucher discount applied to at least one basket item
        $blDiscountApplied = false;

        foreach ($aBasketItems as $aBasketItem) {
            // If discount was already applied for the voucher to at least one basket items, then break
            if ($blDiscountApplied and !empty($oSeries->oxvoucherseries__oxcalculateonce->value)) {
                break;
            }

            $oDiscountPrice->setPrice($aBasketItem['discount']);
            $oProductPrice->setPrice($aBasketItem['price']);

            // Individual voucher is not multiplied by article amount
            if (!$oSeries->oxvoucherseries__oxcalculateonce->value) {
                $oDiscountPrice->multiply($aBasketItem['amount']);
                $oProductPrice->multiply($aBasketItem['amount']);
            }

            $oVoucherPrice->add($oDiscountPrice->getBruttoPrice());
            $oProductTotal->add($oProductPrice->getBruttoPrice());

            if (!empty($aBasketItem['discount'])) {
                $blDiscountApplied = true;
            }
        }

        $dVoucher = $oVoucherPrice->getBruttoPrice();
        $dProduct = $oProductTotal->getBruttoPrice();

        if ($dVoucher > $dProduct) {
            return $dProduct;
        }

        return $dVoucher;
    }

    /**
     * Returns the discount value used, if voucher is applied only for specific categories.
     *
     * @param double $dPrice price to calculate discount on it
     *
     * @throws oxVoucherException exception
     *
     * @deprecated on b-dev (2015-03-31); Use function _getCategoryDiscountValue()
     *
     * @return double
     */
    protected function _getCategoryDiscoutValue($dPrice)
    {
        return $this->_getCategoryDiscountValue($dPrice);
    }

    /**
     * Returns the discount value used, if voucher is applied only for specific categories.
     *
     * @param double $dPrice price to calculate discount on it
     *
     * @throws oxVoucherException exception
     *
     * @return double
     */
    protected function _getCategoryDiscountValue($dPrice)
    {
        $oDiscount = $this->_getSerieDiscount();
        $aBasketItems = $this->_getBasketItems($oDiscount);

        // Basket Item Count and isAdmin check (unable to access property $oOrder->_getOrderBasket()->_blSkipVouchersAvailabilityChecking)
        if (!count($aBasketItems) && !$this->isAdmin()) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\VoucherException::class);
            $oEx->setMessage('ERROR_MESSAGE_VOUCHER_NOVOUCHER');
            $oEx->setVoucherNr($this->oxvouchers__oxvouchernr->value);
            throw $oEx;
        }

        $oProductPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);
        $oProductTotal = oxNew(\OxidEsales\Eshop\Core\Price::class);

        foreach ($aBasketItems as $aBasketItem) {
            $oProductPrice->setPrice($aBasketItem['price']);
            $oProductPrice->multiply($aBasketItem['amount']);
            $oProductTotal->add($oProductPrice->getBruttoPrice());
        }

        $dProduct = $oProductTotal->getBruttoPrice();
        $dVoucher = $oDiscount->getAbsValue($dProduct);

        return ($dVoucher > $dProduct) ? $dProduct : $dVoucher;
    }

    /**
     * Extra getter to guarantee compatibility with templates
     *
     * @param string $sName name of variable to get
     *
     * @return string
     */
    public function __get($sName)
    {
        switch ($sName) {
            // simple voucher mapping
            case 'sVoucherId':
                return $this->getId();
                break;
            case 'sVoucherNr':
                return $this->oxvouchers__oxvouchernr;
                break;
            case 'fVoucherdiscount':
                return $this->oxvouchers__oxdiscount;
                break;
        }
        return parent::__get($sName);
    }

    /**
     * Returns a configured value for voucher timeouts or a default
     * of 3 hours if not configured
     *
     * @return integer Seconds a voucher can stay in status reserved
     */
    protected function _getVoucherTimeout()
    {
        $iVoucherTimeout = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('iVoucherTimeout') ?:
            3 * 3600;

        return $iVoucherTimeout;
    }
}
