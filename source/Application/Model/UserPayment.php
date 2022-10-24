<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use oxRegistry;
use oxDb;

/**
 * User payment manager.
 * Performs assigning, loading, inserting and updating functions for
 * user payment.
 */
class UserPayment extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Name of current class
     *
     * @var string
     */
    protected $_sClassName = 'oxuserpayment';

    /**
     * Payment info object
     *
     * @var \OxidEsales\Eshop\Application\Model\Payment
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
        if (!$this->_aDynValues) {
            $sRawDynValue = '';
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
