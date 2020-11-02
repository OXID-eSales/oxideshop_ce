<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * User payment manager.
 * Performs assigning, loading, inserting and updating functions for
 * user payment.
 */
class UserPayment extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Name of current class.
     *
     * @var string
     */
    protected $_sClassName = 'oxuserpayment';

    /**
     * Payment info object.
     *
     * @var \OxidEsales\Eshop\Application\Model\Payment
     */
    protected $_oPayment = null;

    /**
     * current dyn values.
     *
     * @var array
     */
    protected $_aDynValues = null;

    /**
     * Special getter for oxpayments__oxdesc field.
     *
     * @param string $sName name of field
     *
     * @return string
     */
    public function __get($sName)
    {
        //due to compatibility with templates
        if ('oxpayments__oxdesc' === $sName) {
            if (null === $this->_oPayment) {
                $this->_oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
                $this->_oPayment->load($this->oxuserpayments__oxpaymentsid->value);
            }

            return $this->_oPayment->oxpayments__oxdesc;
        }

        if ('aDynValues' === $sName) {
            if (null === $this->_aDynValues) {
                $this->_aDynValues = $this->getDynValues();
            }

            return $this->_aDynValues;
        }

        return parent::__get($sName);
    }

    /**
     * Class constructor. Sets payment key for encoding sensitive data and.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxuserpayments');
    }

    /**
     * Loads user payment object.
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
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "insert" in next major
     */
    protected function _insert() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return parent::_insert();
    }

    /**
     * Get user payment by payment id.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser        user object
     * @param string                                   $sPaymentType payment type
     *
     * @return bool
     */
    public function getPaymentByPaymentType($oUser = null, $sPaymentType = null)
    {
        $blGet = false;
        if ($oUser && null !== $sPaymentType) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = 'select oxpaymentid from oxorder where oxpaymenttype = :oxpaymenttype and
                    oxuserid = :oxuserid order by oxorderdate desc';
            $params = [
                ':oxpaymenttype' => $sPaymentType,
                ':oxuserid' => $oUser->getId(),
            ];

            if (($sOxId = $oDb->getOne($sQ, $params))) {
                $blGet = $this->load($sOxId);
            }
        }

        return $blGet;
    }

    /**
     * Returns an array of dyn payment values.
     *
     * @return array
     */
    public function getDynValues()
    {
        if (!$this->_aDynValues) {
            $sRawDynValue = null;
            if (\is_object($this->oxuserpayments__oxvalue)) {
                $sRawDynValue = $this->oxuserpayments__oxvalue->getRawValue();
            }

            $this->_aDynValues = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($sRawDynValue);
        }

        return $this->_aDynValues;
    }

    /**
     * sets the dyn values.
     *
     * @param array $aDynValues the array of dy values
     */
    public function setDynValues($aDynValues): void
    {
        $this->_aDynValues = $aDynValues;
    }
}
