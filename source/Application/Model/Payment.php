<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxDb;

/**
 * Payment manager.
 * Performs payment methods, such as assigning to someone, returning value etc.
 *
 */
class Payment extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Consider for calculation of base sum - Value of all goods in basket
     *
     * @var int
     */
    const PAYMENT_ADDSUMRULE_ALLGOODS = 1;

    /**
     * Consider for calculation of base sum - Discounts
     *
     * @var int
     */
    const PAYMENT_ADDSUMRULE_DISCOUNTS = 2;

    /**
     * Consider for calculation of base sum - Vouchers
     *
     * @var int
     */
    const PAYMENT_ADDSUMRULE_VOUCHERS = 4;

    /**
     * Consider for calculation of base sum - Shipping costs
     *
     * @var int
     */
    const PAYMENT_ADDSUMRULE_SHIPCOSTS = 8;

    /**
     * Consider for calculation of base sum - Gift Wrapping/Greeting Card
     *
     * @var int
     */
    const PAYMENT_ADDSUMRULE_GIFTS = 16;

    /**
     * User groups object (default null).
     *
     * @var object
     */
    protected $_oGroups = null;

    /**
     * Countries assigned to current payment. Value from outside accessible
     * by calling \OxidEsales\Eshop\Application\Model\Payment::getCountries
     *
     * @var array
     */
    protected $_aCountries = null;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxpayment';

    /**
     * current dyn values
     *
     * @var array
     */
    protected $_aDynValues = null;

    /**
     * payment error type
     *
     * @var int
     */
    protected $_iPaymentError = null;

    /**
     * Payment VAT config
     *
     * @var bool
     */
    protected $_blPaymentVatOnTop = false;

    /**
     * Payment price
     *
     * @var oxPrice
     */
    protected $_oPrice = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        $this->setPaymentVatOnTop($this->getConfig()->getConfigParam('blPaymentVatOnTop'));
        parent::__construct();
        $this->init('oxpayments');
    }

    /**
     * Payment VAT config setter
     *
     * @param bool $blOnTop Payment vat config
     */
    public function setPaymentVatOnTop($blOnTop)
    {
        $this->_blPaymentVatOnTop = $blOnTop;
    }

    /**
     * Payment groups getter. Returns groups list
     *
     * @return oxList
     */
    public function getGroups()
    {
        if ($this->_oGroups == null && ($sOxid = $this->getId())) {
            // user groups
            $this->_oGroups = oxNew('oxlist', 'oxgroups');
            $sViewName = getViewName("oxgroups", $this->getLanguage());

            // performance
            $sSelect = "select {$sViewName}.* from {$sViewName}, oxobject2group
                        where oxobject2group.oxobjectid = :oxobjectid
                        and oxobject2group.oxgroupsid = {$sViewName}.oxid ";
            $this->_oGroups->selectString($sSelect, [
                ':oxobjectid' => $sOxid
            ]);
        }

        return $this->_oGroups;
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

    /**
     * Sets a single dyn value
     *
     * @param mixed $oKey the key
     * @param mixed $oVal the value
     */
    public function setDynValue($oKey, $oVal)
    {
        $this->_aDynValues[$oKey] = $oVal;
    }

    /**
     * Returns an array of dyn payment values
     *
     * @return array
     */
    public function getDynValues()
    {
        if (!$this->_aDynValues) {
            $sRawDynValue = null;
            if (is_object($this->oxpayments__oxvaldesc)) {
                $sRawDynValue = $this->oxpayments__oxvaldesc->getRawValue();
            }

            $this->_aDynValues = \OxidEsales\Eshop\Core\Registry::getUtils()->assignValuesFromText($sRawDynValue);
        }

        return $this->_aDynValues;
    }

    /**
     * Returns additional taxes to base article price.
     *
     * @param double $dBasePrice Base article price
     *
     * @return double
     */
    public function getPaymentValue($dBasePrice)
    {
        if ($this->oxpayments__oxaddsumtype->value == "%") {
            $dRet = $dBasePrice * $this->oxpayments__oxaddsum->value / 100;
        } else {
            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $dRet = $this->oxpayments__oxaddsum->value * $oCur->rate;
        }

        if (($dRet * -1) > $dBasePrice) {
            $dRet = $dBasePrice;
        }

        return $dRet;
    }

    /**
     * Returns base basket price for payment cost calculations. Price depends on
     * payment setup (payment administration)
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket oxBasket object
     *
     * @return double
     */
    public function getBaseBasketPriceForPaymentCostCalc($oBasket)
    {
        $dBasketPrice = 0;
        $iRules = $this->oxpayments__oxaddsumrules->value;

        // products brutto price
        if (!$iRules || ($iRules & self::PAYMENT_ADDSUMRULE_ALLGOODS)) {
            $dBasketPrice += $oBasket->getProductsPrice()->getSum($oBasket->isCalculationModeNetto());
        }

        // discounts
        if ((!$iRules || ($iRules & self::PAYMENT_ADDSUMRULE_DISCOUNTS)) &&
            ($oCosts = $oBasket->getTotalDiscount())
        ) {
            $dBasketPrice -= $oCosts->getPrice();
        }

        // vouchers
        if (!$iRules || ($iRules & self::PAYMENT_ADDSUMRULE_VOUCHERS)) {
            $dBasketPrice -= $oBasket->getVoucherDiscValue();
        }

        // delivery
        if ((!$iRules || ($iRules & self::PAYMENT_ADDSUMRULE_SHIPCOSTS)) &&
            ($oCosts = $oBasket->getCosts('oxdelivery'))
        ) {
            if ($oBasket->isCalculationModeNetto()) {
                $dBasketPrice += $oCosts->getNettoPrice();
            } else {
                $dBasketPrice += $oCosts->getBruttoPrice();
            }
        }

        // wrapping
        if (($iRules & self::PAYMENT_ADDSUMRULE_GIFTS) &&
            ($oCosts = $oBasket->getCosts('oxwrapping'))
        ) {
            if ($oBasket->isCalculationModeNetto()) {
                $dBasketPrice += $oCosts->getNettoPrice();
            } else {
                $dBasketPrice += $oCosts->getBruttoPrice();
            }
        }

        // gift card
        if (($iRules & self::PAYMENT_ADDSUMRULE_GIFTS) &&
            ($oCosts = $oBasket->getCosts('oxgiftcard'))
        ) {
            if ($oBasket->isCalculationModeNetto()) {
                $dBasketPrice += $oCosts->getNettoPrice();
            } else {
                $dBasketPrice += $oCosts->getBruttoPrice();
            }
        }

        return $dBasketPrice;
    }

    /**
     * Returns price object for current payment applied on basket
     *
     * @param \OxidEsales\Eshop\Application\Model\UserBasket $oBasket session basket
     */
    public function calculate($oBasket)
    {
        //getting basket price with applied discounts and vouchers
        $dPrice = $this->getPaymentValue($this->getBaseBasketPriceForPaymentCostCalc($oBasket));

        if (!$dPrice) {
            $dPrice = 0;
        }
        // calculating total price
        $oPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);
        $oPrice->setNettoMode($this->_blPaymentVatOnTop);

        $oPrice->setPrice($dPrice);
        if ($dPrice > 0) {
            $oPrice->setVat($oBasket->getAdditionalServicesVatPercent());
        }

        $this->_oPrice = $oPrice;
    }

    /**
     * Returns calculated price.
     *
     * @return oxPrice
     */
    public function getPrice()
    {
        return $this->_oPrice;
    }

    /**
     * Returns formatted netto price.
     *
     * @deprecated in v4.8/5.1 on 2013-10-14; for formatting use oxPrice smarty plugin
     *
     * @return string
     */
    public function getFNettoPrice()
    {
        if ($this->getPrice()) {
            return \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($this->getPrice()->getNettoPrice());
        }
    }

    /**
     * Returns formatted brutto price.
     *
     * @deprecated in v4.8/5.1 on 2013-10-14; for formatting use oxPrice smarty plugin
     *
     * @return string
     */
    public function getFBruttoPrice()
    {
        if ($this->getPrice()) {
            return \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($this->getPrice()->getBruttoPrice());
        }
    }

    /**
     * Returns formatted vat value.
     *
     * @deprecated in v4.8/5.1 on 2013-10-14; for formatting use oxPrice smarty plugin
     *
     * @return string
     */
    public function getFPriceVat()
    {
        if ($this->getPrice()) {
            return \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($this->getPrice()->getVatValue());
        }
    }

    /**
     * Returns array of country Ids which are assigned to current payment
     *
     * @return array
     */
    public function getCountries()
    {
        if ($this->_aCountries === null) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $this->_aCountries = [];
            $sSelect = 'select oxobjectid from oxobject2payment 
                where oxpaymentid = :oxpaymentid and oxtype = :oxtype ';
            $rs = $oDb->getCol($sSelect, [
                ':oxpaymentid' => $this->getId(),
                ':oxtype' => 'oxcountry'
            ]);
            $this->_aCountries = $rs;
        }

        return $this->_aCountries;
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOxId Object ID(default null)
     *
     * @return bool
     */
    public function delete($sOxId = null)
    {
        if (parent::delete($sOxId)) {
            $sOxId = $sOxId ? $sOxId : $this->getId();
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

            // deleting payment related data
            $rs = $oDb->execute("delete from oxobject2payment where oxpaymentid = :oxpaymentid", [
                ':oxpaymentid' => $sOxId
            ]);

            return $rs->EOF;
        }

        return false;
    }

    /**
     * Function checks if loaded payment is valid to current basket
     *
     * @param array                                    $aDynValue    dynamical value (in this case oxidcreditcard and oxiddebitnote are checked only)
     * @param string                                   $sShopId      id of current shop
     * @param \OxidEsales\Eshop\Application\Model\User $oUser        the current user
     * @param double                                   $dBasketPrice the current basket price (oBasket->dPrice)
     * @param string                                   $sShipSetId   the current ship set
     *
     * @return bool true if payment is valid
     */
    public function isValidPayment($aDynValue, $sShopId, $oUser, $dBasketPrice, $sShipSetId)
    {
        $myConfig = $this->getConfig();
        if ($this->oxpayments__oxid->value == 'oxempty') {
            // inactive or blOtherCountryOrder is off
            if (!$this->oxpayments__oxactive->value || !$myConfig->getConfigParam("blOtherCountryOrder")) {
                $this->_iPaymentError = -2;

                return false;
            }
            if (count(
                \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\DeliverySetList::class)
                    ->getDeliverySetList(
                        $oUser,
                        $oUser->getActiveCountry()
                    )
            )
            ) {
                $this->_iPaymentError = -3;

                return false;
            }

            return true;
        }

        $mxValidationResult = \OxidEsales\Eshop\Core\Registry::getInputValidator()->validatePaymentInputData($this->oxpayments__oxid->value, $aDynValue);

        if (is_integer($mxValidationResult)) {
            $this->_iPaymentError = $mxValidationResult;

            return false;
        } elseif ($mxValidationResult === false) {
            $this->_iPaymentError = 1;

            return false;
        }

        $oCur = $myConfig->getActShopCurrencyObject();
        $dBasketPrice = $dBasketPrice / $oCur->rate;

        if ($sShipSetId) {
            $aPaymentList = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\PaymentList::class)->getPaymentList($sShipSetId, $dBasketPrice, $oUser);

            if (!array_key_exists($this->getId(), $aPaymentList)) {
                $this->_iPaymentError = -3;

                return false;
            }
        } else {
            $this->_iPaymentError = -2;

            return false;
        }

        return true;
    }

    /**
     * Payment error number getter
     *
     * @return int
     */
    public function getPaymentErrorNumber()
    {
        return $this->_iPaymentError;
    }
}
