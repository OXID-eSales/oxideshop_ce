<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxDb;

/**
 * User payment manager.
 * Performs assigning, loading, inserting and updating functions for
 * user payment.
 *
 */
class UserPayment extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    // you can change this if you want more security
    // DO NOT !! CHANGE THIS FILE AND STORE CREDIT CARD INFORMATION
    // THIS IS MORE THAN LIKELY ILLEGAL !!
    // CHECK YOUR CREDIT CARD CONTRACT

    /**
     * Name of current class
     *
     * @var string
     */
    protected $_sClassName = 'oxuserpayment';

    /**
     * Store credit card information in db or not
     *
     * @var bool
     */
    protected $_blStoreCreditCardInfo = null;

    /**
     * Payment info object
     *
     * @var oxpayment
     */
    protected $_oPayment = null;

    /**
     * current dyn values
     *
     * @var array
     */
    protected $_aDynValues = null;

    /**
     * Special getter for oxpayments__oxdesc field
     *
     * @param string $sName name of field
     *
     * @return string
     */
    public function __get($sName)
    {
        //due to compatibility with templates
        if ($sName == 'oxpayments__oxdesc') {
            if ($this->_oPayment === null) {
                $this->_oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
                $this->_oPayment->load($this->oxuserpayments__oxpaymentsid->value);
            }

            return $this->_oPayment->oxpayments__oxdesc;
        }

        if ($sName == 'aDynValues') {
            if ($this->_aDynValues === null) {
                $this->_aDynValues = $this->getDynValues();
            }

            return $this->_aDynValues;
        }

        return parent::__get($sName);
    }

    /**
     * Class constructor. Sets payment key for encoding sensitive data and
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxuserpayments');
        $this->setStoreCreditCardInfo(\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blStoreCreditCardInfo'));
    }

    /**
     * Loads user payment object
     *
     * @param string $sOxId oxuserpayment id
     *
     * @return mixed
     */
    public function load($sOxId)
    {
        $sSelect = 'select oxid, oxuserid, oxpaymentsid, oxvalue
                    from oxuserpayments where oxid = ' . \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote($sOxId);

        return $this->assignRecord($sSelect);
    }


    /**
     * Inserts payment information to DB. Returns insert status.
     *
     * @return bool
     */
    protected function _insert()
    {
        // we do not store credit card information
        // check and in case skip it
        if (!$this->getStoreCreditCardInfo() && $this->oxuserpayments__oxpaymentsid->value == 'oxidcreditcard') {
            return true;
        }

        $blRet = parent::_insert();

        return $blRet;
    }

    /**
     * Set store or not credit card information in db
     *
     * @param bool $blStoreCreditCardInfo store or not credit card info
     */
    public function setStoreCreditCardInfo($blStoreCreditCardInfo)
    {
        $this->_blStoreCreditCardInfo = $blStoreCreditCardInfo;
    }

    /**
     * Get store or not credit card information in db parameter
     *
     * @return bool
     */
    public function getStoreCreditCardInfo()
    {
        return $this->_blStoreCreditCardInfo;
    }

    /**
     * Get user payment by payment id
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser        user object
     * @param string                                   $sPaymentType payment type
     *
     * @return bool
     */
    public function getPaymentByPaymentType($oUser = null, $sPaymentType = null)
    {
        $blGet = false;
        if ($oUser && $sPaymentType != null) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = 'select oxpaymentid from oxorder where oxpaymenttype = :oxpaymenttype and
                    oxuserid = :oxuserid order by oxorderdate desc';
            $params = [
                ':oxpaymenttype' => $sPaymentType,
                ':oxuserid' => $oUser->getId()
            ];

            if (($sOxId = $oDb->getOne($sQ, $params))) {
                $blGet = $this->load($sOxId);
            }
        }

        return $blGet;
    }

    /**
     * Returns an array of dyn payment values
     *
     * @return array
     */
    public function getDynValues()
    {
        if (!$this->getStoreCreditCardInfo() && $this->oxuserpayments__oxpaymentsid->value == 'oxidcreditcard') {
            return null;
        }

        if (!$this->_aDynValues) {
            $sRawDynValue = null;
            if (is_object($this->oxuserpayments__oxvalue)) {
                $sRawDynValue = $this->oxuserpayments__oxvalue->getRawValue();
            }

            $this->_aDynValues = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($sRawDynValue);
        }

        return $this->_aDynValues;
    }

    /**
     * sets the dyn values
     *
     * @param array $aDynValues the array of dy values
     */
    public function setDynValues($aDynValues)
    {
        $this->_aDynValues = $aDynValues;
    }
}
