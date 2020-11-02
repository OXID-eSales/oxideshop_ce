<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin user payment settings manager.
 * Collects user payment settings, updates it on user submit, etc.
 * Admin Menu: User Administration -> Users -> Payment.
 */
class UserPayment extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * (default false).
     *
     * @var bool
     */
    protected $_blDelete = false;

    /**
     * Selected user.
     *
     * @var object
     */
    protected $_oActiveUser = null;

    /**
     * Selected user payment.
     *
     * @var string
     */
    protected $_sPaymentId = null;

    /**
     * List of all payments.
     *
     * @var object
     */
    protected $_oPaymentTypes = null;

    /**
     * Selected user payment.
     *
     * @var object
     */
    protected $_oUserPayment = null;

    /**
     * List of all user payments.
     *
     * @var object
     */
    protected $_oUserPayments = null;

    /**
     * Executes parent method parent::render(), creates oxlist and oxuser objects
     * and returns the name of the template file.
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData['edit'] = $this->getSelUserPayment();
        $this->_aViewData['oxpaymentid'] = $this->getPaymentId();
        $this->_aViewData['paymenttypes'] = $this->getPaymentTypes();
        $this->_aViewData['edituser'] = $this->getUser();
        $this->_aViewData['userpayments'] = $this->getUserPayments();
        $sOxId = $this->getEditObjectId();

        if (!$this->_allowAdminEdit($sOxId)) {
            $this->_aViewData['readonly'] = true;
        }

        return 'user_payment.tpl';
    }

    /**
     * Saves user payment settings.
     */
    public function save(): void
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        if ($this->_allowAdminEdit($soxId)) {
            $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editval');
            $aDynvalues = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('dynvalue');

            if (isset($aDynvalues)) {
                // store the dynvalues
                $aParams['oxuserpayments__oxvalue'] = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesToText($aDynvalues);
            }

            if ('-1' === $aParams['oxuserpayments__oxid']) {
                $aParams['oxuserpayments__oxid'] = null;
            }

            $oAdress = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
            $oAdress->assign($aParams);
            $oAdress->save();
        }
    }

    /**
     * Deletes selected user payment information.
     */
    public function delPayment(): void
    {
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editval');
        $soxId = $this->getEditObjectId();
        if ($this->_allowAdminEdit($soxId)) {
            if ('-1' !== $aParams['oxuserpayments__oxid']) {
                $oAdress = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
                if ($oAdress->load($aParams['oxuserpayments__oxid'])) {
                    $this->_blDelete = (bool)$oAdress->delete();
                }
            }
        }
    }

    /**
     * Returns selected user.
     *
     * @return \OxidEsales\Eshop\Application\Model\User|false
     */
    public function getUser()
    {
        if (null === $this->_oActiveUser) {
            $this->_oActiveUser = false;
            $sOxId = $this->getEditObjectId();
            if (isset($sOxId) && '-1' !== $sOxId) {
                // load object
                $this->_oActiveUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
                $this->_oActiveUser->load($sOxId);
            }
        }

        return $this->_oActiveUser;
    }

    /**
     * Returns selected Payment Id.
     *
     * @return object
     */
    public function getPaymentId()
    {
        if (null === $this->_sPaymentId) {
            $this->_sPaymentId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('oxpaymentid');
            if (!$this->_sPaymentId || $this->_blDelete) {
                if ($oUser = $this->getUser()) {
                    $oUserPayments = $oUser->getUserPayments();
                    if (isset($oUserPayments[0])) {
                        $this->_sPaymentId = $oUserPayments[0]->oxuserpayments__oxid->value;
                    }
                }
            }
            if (!$this->_sPaymentId) {
                $this->_sPaymentId = '-1';
            }
        }

        return $this->_sPaymentId;
    }

    /**
     * Returns selected Payment Id.
     *
     * @return object
     */
    public function getPaymentTypes()
    {
        if (null === $this->_oPaymentTypes) {
            // all paymenttypes
            $this->_oPaymentTypes = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $this->_oPaymentTypes->init('oxpayment');
            $oListObject = $this->_oPaymentTypes->getBaseObject();
            $oListObject->setLanguage(\OxidEsales\Eshop\Core\Registry::getLang()->getObjectTplLanguage());
            $this->_oPaymentTypes->getList();
        }

        return $this->_oPaymentTypes;
    }

    /**
     * Returns selected Payment.
     *
     * @return object
     */
    public function getSelUserPayment()
    {
        if (null === $this->_oUserPayment) {
            $this->_oUserPayment = false;
            $sPaymentId = $this->getPaymentId();
            if ('-1' !== $sPaymentId && isset($sPaymentId)) {
                $this->_oUserPayment = oxNew(\OxidEsales\Eshop\Application\Model\UserPayment::class);
                $this->_oUserPayment->load($sPaymentId);
                $sTemplate = $this->_oUserPayment->oxuserpayments__oxvalue->value;

                // generate selected paymenttype
                $oPaymentTypes = $this->getPaymentTypes();
                foreach ($oPaymentTypes as $oPayment) {
                    if ($oPayment->oxpayments__oxid->value === $this->_oUserPayment->oxuserpayments__oxpaymentsid->value) {
                        $oPayment->selected = 1;
                        // if there are no values assigned we set default from paymenttype
                        if (!$sTemplate) {
                            $sTemplate = $oPayment->oxpayments__oxvaldesc->value;
                        }
                        break;
                    }
                }
                $this->_oUserPayment->setDynValues(\OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($sTemplate));
            }
        }

        return $this->_oUserPayment;
    }

    /**
     * Returns selected Payment Id.
     *
     * @return object
     */
    public function getUserPayments()
    {
        if (null === $this->_oUserPayments) {
            $this->_oUserPayments = false;
            if ($oUser = $this->getUser()) {
                $sTplLang = \OxidEsales\Eshop\Core\Registry::getLang()->getObjectTplLanguage();
                $sPaymentId = $this->getPaymentId();
                $this->_oUserPayments = $oUser->getUserPayments();
                // generate selected
                foreach ($this->_oUserPayments as $oUserPayment) {
                    $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);
                    $oPayment->setLanguage($sTplLang);
                    $oPayment->load($oUserPayment->oxuserpayments__oxpaymentsid->value);
                    $oUserPayment->oxpayments__oxdesc = clone $oPayment->oxpayments__oxdesc;
                    if ($oUserPayment->oxuserpayments__oxid->value === $sPaymentId) {
                        $oUserPayment->selected = 1;
                        break;
                    }
                }
            }
        }

        return $this->_oUserPayments;
    }
}
