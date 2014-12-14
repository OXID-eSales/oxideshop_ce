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
 * Order delivery manager.
 * Currently calculates price/costs.
 *
 */
class oxDelivery extends oxI18n
{

    /**
     * Calculation rule
     */
    const CALCULATION_RULE_ONCE_PER_CART = 0;
    const CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT = 1;
    const CALCULATION_RULE_FOR_EACH_PRODUCT = 2;

    /**
     * Condition type
     */
    const CONDITION_TYPE_PRICE = 'p';
    const CONDITION_TYPE_AMOUNT = 'a';
    const CONDITION_TYPE_SIZE = 's';
    const CONDITION_TYPE_WEIGHT = 'w';

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxdelivery';

    /**
     * Total count of product items which are covered by current delivery
     * (used for caching purposes across several methods)
     *
     * @var double
     */
    protected $_iItemCnt = 0;

    /**
     * Total count of products which are covered by current delivery
     * (used for caching purposes across several methods)
     *
     * @var double
     */
    protected $_iProdCnt = 0;

    /**
     * Total price of products which are covered by current delivery
     * (used for caching purposes across several methods)
     *
     * @var double
     */
    protected $_dPrice = 0;

    /**
     * Current delivery price object which keeps price info
     *
     * @var oxPrice
     */
    protected $_oPrice = null;

    /**
     * Article Ids which are assigned to current delivery
     *
     * @var array
     */
    protected $_aArtIds = null;

    /**
     * Category Ids which are assigned to current delivery
     *
     * @var array
     */
    protected $_aCatIds = null;

    /**
     * If article has free shipping
     *
     * @var bool
     */
    protected $_blFreeShipping = true;

    /**
     * Product list storage
     *
     * @var array
     */
    protected static $_aProductList = array();

    /**
     * Delivery VAT config
     *
     * @var bool
     */
    protected $_blDelVatOnTop = false;

    /**
     * Countries ISO assigned to current delivery.
     *
     * @var array
     */
    protected $_aCountriesISO = null;

    /**
     * RDFa delivery sets assigned to current delivery.
     *
     * @var array
     */
    protected $_aRDFaDeliverySet = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxdelivery');
        $this->setDelVatOnTop($this->getConfig()->getConfigParam('blDeliveryVatOnTop'));
    }

    /**
     * Delivery VAT config setter
     *
     * @param bool $blOnTop delivery vat config
     */
    public function setDelVatOnTop($blOnTop)
    {
        $this->_blDelVatOnTop = $blOnTop;
    }

    /**
     * Collects article Ids which are assigned to current delivery
     *
     * @return array
     */
    public function getArticles()
    {
        if (is_null($this->_aArtIds)) {
            $oDb = oxDb::getDb();
            $sQ = "select oxobjectid from oxobject2delivery where oxdeliveryid=" . $oDb->quote($this->getId()) . " and oxtype = 'oxarticles'";
            $aArtIds = $oDb->getCol($sQ);
            $this->_aArtIds = $aArtIds;
        }

        return $this->_aArtIds;
    }

    /**
     * Collects category Ids which are assigned to current delivery
     *
     * @return array
     */
    public function getCategories()
    {
        if (is_null($this->_aCatIds)) {
            $oDb = oxDb::getDb();
            $sQ = "select oxobjectid from oxobject2delivery where oxdeliveryid=" . $oDb->quote($this->getId()) . " and oxtype = 'oxcategories'";
            $aCatIds = $oDb->getCol($sQ);
            $this->_aCatIds = $aCatIds;
        }

        return $this->_aCatIds;
    }

    /**
     * Checks if delivery has assigned articles
     *
     * @return bool
     */
    public function hasArticles()
    {
        return ( bool ) count($this->getArticles());
    }

    /**
     * Checks if delivery has assigned categories
     *
     * @return bool
     */
    public function hasCategories()
    {
        return ( bool ) count($this->getCategories());
    }

    /**
     * Returns amount (total net price/weight/volume/Amount) on which delivery price is applied
     *
     * @param oxBasketItem $oBasketItem basket item object
     *
     * @return double
     */
    public function getDeliveryAmount($oBasketItem)
    {
        $dAmount = 0;
        $oProduct = $oBasketItem->getArticle(false);

        $blExclNonMaterial = $this->getConfig()->getConfigParam('blExclNonMaterialFromDelivery');

        // mark free shipping products
        if ($oProduct->oxarticles__oxfreeshipping->value || ($oProduct->oxarticles__oxnonmaterial->value && $blExclNonMaterial)) {
            if ($this->_blFreeShipping !== false) {
                $this->_blFreeShipping = true;
            }
        } else {

            $this->_blFreeShipping = false;

            switch ($this->getConditionType()) {
                case self::CONDITION_TYPE_PRICE: // price
                    if ($this->getCalculationRule() == self::CALCULATION_RULE_FOR_EACH_PRODUCT) {
                        $dAmount += $oProduct->getPrice()->getPrice();
                    } else {
                        $dAmount += $oBasketItem->getPrice()->getPrice(); // price// currency conversion must allready be done in price class / $oCur->rate; // $oBasketItem->oPrice->getPrice() / $oCur->rate;
                    }
                    break;
                case self::CONDITION_TYPE_WEIGHT: // weight
                    if ($this->getCalculationRule() == self::CALCULATION_RULE_FOR_EACH_PRODUCT) {
                        $dAmount += $oProduct->getWeight();
                    } else {
                        $dAmount += $oBasketItem->getWeight();
                    }
                    break;
                case self::CONDITION_TYPE_SIZE: // size
                    $dAmount += $oProduct->getSize();
                    if ($this->getCalculationRule() != self::CALCULATION_RULE_FOR_EACH_PRODUCT) {
                        $dAmount *= $oBasketItem->getAmount();
                    }
                    break;
                case self::CONDITION_TYPE_AMOUNT: // amount
                    $dAmount += $oBasketItem->getAmount();
                    break;
            }

            if ($oBasketItem->getPrice()) {
                $this->_dPrice += $oBasketItem->getPrice()->getPrice();
            }
        }

        return $dAmount;
    }

    /**
     * Delivery price setter
     *
     * @param oxPrice $oPrice delivery price to set
     */
    public function setDeliveryPrice($oPrice)
    {
        $this->_oPrice = $oPrice;
    }

    /**
     * Returns oxPrice object for delivery costs
     *
     * @param double $dVat delivery vat
     *
     * @return oxPrice
     */
    public function getDeliveryPrice($dVat = null)
    {
        if ($this->_oPrice === null) {

            // loading oxPrice object for final price calculation
            $oPrice = oxNew('oxPrice');
            $oPrice->setNettoMode($this->_blDelVatOnTop);
            $oPrice->setVat($dVat);

            // if article is free shipping, price for delivery will be not calculated
            if (!$this->_blFreeShipping) {
                $oPrice->add($this->_getCostSum());
            }
            $this->setDeliveryPrice($oPrice);
        }

        return $this->_oPrice;
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOxId Object ID (default null)
     *
     * @return bool
     */
    public function delete($sOxId = null)
    {
        if (!$sOxId) {
            $sOxId = $this->getId();
        }
        if (!$sOxId) {
            return false;
        }


        $oDb = oxDb::getDb();
        $sQ = "delete from `oxobject2delivery` where `oxobject2delivery`.`oxdeliveryid` = " . $oDb->quote($sOxId);
        $oDb->execute($sQ);

        return parent::delete($sOxId);
    }

    /**
     * Checks if delivery fits for current basket
     *
     * @param oxBasket $oBasket shop basket
     *
     * @return bool
     */
    public function isForBasket($oBasket)
    {
        // amount for conditional check
        $blHasArticles = $this->hasArticles();
        $blHasCategories = $this->hasCategories();
        $blUse = true;
        $iAmount = 0;
        $blForBasket = false;

        // category & article check
        if ($blHasCategories || $blHasArticles) {
            $blUse = false;

            $aDeliveryArticles = $this->getArticles();
            $aDeliveryCategories = $this->getCategories();

            foreach ($oBasket->getContents() as $oContent) {

                //V FS#1954 - load delivery for variants from parent article
                $oArticle = $oContent->getArticle(false);
                $sProductId = $oArticle->getProductId();
                $sParentId = $oArticle->getParentId();

                if ($blHasArticles && (in_array($sProductId, $aDeliveryArticles) || ($sParentId && in_array($sParentId, $aDeliveryArticles)))) {
                    $blUse = true;
                    $iArtAmount = $this->getDeliveryAmount($oContent);
                    if ($this->getCalculationRule() != self::CALCULATION_RULE_ONCE_PER_CART) {
                        if ($this->_isForArticle($oContent, $iArtAmount)) {
                            $blForBasket = true;
                        }
                    }
                    if (!$blForBasket) {
                        $iAmount += $iArtAmount;
                    }

                } elseif ($blHasCategories) {

                    if (isset(self::$_aProductList[$sProductId])) {
                        $oProduct = self::$_aProductList[$sProductId];
                    } else {
                        $oProduct = oxNew('oxArticle');
                        $oProduct->setSkipAssign(true);

                        if (!$oProduct->load($sProductId)) {
                            continue;
                        }

                        $oProduct->setId($sProductId);
                        self::$_aProductList[$sProductId] = $oProduct;
                    }

                    foreach ($aDeliveryCategories as $sCatId) {

                        if ($oProduct->inCategory($sCatId)) {
                            $blUse = true;
                            $iArtAmount = $this->getDeliveryAmount($oContent);
                            if ($this->getCalculationRule() != self::CALCULATION_RULE_ONCE_PER_CART) {
                                if ($this->_isForArticle($oContent, $iArtAmount)) {
                                    $blForBasket = true;
                                }
                            }
                            if (!$blForBasket) {
                                $iAmount += $iArtAmount;
                            }
                        }
                    }

                }
            }
        } else {
            // regular amounts check
            foreach ($oBasket->getContents() as $oContent) {
                $iArtAmount = $this->getDeliveryAmount($oContent);
                if ($this->getCalculationRule() != self::CALCULATION_RULE_ONCE_PER_CART) {
                    if ($this->_isForArticle($oContent, $iArtAmount)) {
                        $blForBasket = true;
                    }
                }
                if (!$blForBasket) {
                    $iAmount += $iArtAmount;
                }
            }
        }

        /* if ( $this->getConditionType() == self::CONDITION_TYPE_PRICE ) {
             $iAmount = $oBasket->_getDiscountedProductsSum();
         }*/

        //#M1130: Single article in Basket, checked as free shipping, is not buyable (step 3 no payments found)
        if (!$blForBasket && $blUse && ($this->_checkDeliveryAmount($iAmount) || $this->_blFreeShipping)) {
            $blForBasket = true;
        }

        return $blForBasket;
    }

    /**
     * Checks if delivery fits for one article
     *
     * @param object  $oContent   shop basket item
     * @param integer $iArtAmount product amount
     *
     * @return bool
     */
    protected function _isForArticle($oContent, $iArtAmount)
    {
        $blResult = false;
        if (!$this->_blFreeShipping && $this->_checkDeliveryAmount($iArtAmount)) {
            $this->_iItemCnt += $oContent->getAmount();
            $this->_iProdCnt += 1;
            $blResult = true;
        }

        return $blResult;
    }

    /**
     * checks if amount param is ok for this delivery
     *
     * @param double $iAmount amount
     *
     * @return boolean
     */
    protected function _checkDeliveryAmount($iAmount)
    {
        $blResult = false;

        if ($this->getConditionType() == self::CONDITION_TYPE_PRICE) {
            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $iAmount /= $oCur->rate;
        }

        if ($iAmount >= $this->getConditionFrom() && $iAmount <= $this->getConditionTo()) {
            $blResult = true;
        }

        return $blResult;
    }

    /**
     * returns delivery id
     *
     * @param string $sTitle delivery name
     *
     * @return string
     */
    public function getIdByName($sTitle)
    {
        $oDb = oxDb::getDb();
        $sQ = "SELECT `oxid` FROM `" . getViewName('oxdelivery') . "` WHERE `oxtitle` = " . $oDb->quote($sTitle);
        $sId = $oDb->getOne($sQ);

        return $sId;
    }

    /**
     * Returns array of country ISO's which are assigned to current delivery
     *
     * @return array
     */
    public function getCountriesISO()
    {
        if ($this->_aCountriesISO === null) {

            $oDb = oxDb::getDb();
            $this->_aCountriesISO = array();

            $sSelect = "
                SELECT
                    `oxcountry`.`oxisoalpha2`
                FROM `oxcountry`
                    LEFT JOIN `oxobject2delivery` ON `oxobject2delivery`.`oxobjectid` = `oxcountry`.`oxid`
                WHERE `oxobject2delivery`.`oxdeliveryid` = " . $oDb->quote($this->getId()) . "
                    AND `oxobject2delivery`.`oxtype` = 'oxcountry'";

            $rs = $oDb->getCol($sSelect);
            $this->_aCountriesISO = $rs;

        }

        return $this->_aCountriesISO;
    }

    /**
     * Returns condition type (type >= from <= to) : a - amount, s - size, w -weight, p - price
     *
     * @return string
     */
    public function getConditionType()
    {
        return $this->oxdelivery__oxdeltype->value;
    }

    /**
     * Returns condition from value (type >= from <= to)
     *
     * @return string
     */
    public function getConditionFrom()
    {
        return $this->oxdelivery__oxparam->value;
    }

    /**
     * Returns condition to value (type >= from <= to)
     *
     * @return string
     */
    public function getConditionTo()
    {
        return $this->oxdelivery__oxparamend->value;
    }

    /**
     * Returns calculation rule: 0 - Once per Cart; 1 - Once for each different product 2 - For each product
     *
     * @return int
     */
    public function getCalculationRule()
    {
        return $this->oxdelivery__oxfixed->value;
    }

    /**
     * Returns amount cost
     *
     * @return float
     */
    public function getAddSum()
    {
        return $this->oxdelivery__oxaddsum->value;
    }

    /**
     * Returns type of cost: % - percentage; abs - absolute value
     *
     * @return string
     */
    public function getAddSumType()
    {
        return $this->oxdelivery__oxaddsumtype->value;
    }

    /**
     * Calculate multiplier for price calculation
     *
     * @return float|int
     */
    protected function _getMultiplier()
    {
        $dAmount = 0;

        if ($this->getCalculationRule() == self::CALCULATION_RULE_ONCE_PER_CART) {
            $dAmount = 1;
        } elseif ($this->getCalculationRule() == self::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT) {
            $dAmount = $this->_iProdCnt;
        } elseif ($this->getCalculationRule() == self::CALCULATION_RULE_FOR_EACH_PRODUCT) {
            $dAmount = $this->_iItemCnt;
        }

        return $dAmount;
    }

    /**
     * Calculate cost sum
     *
     * @return float
     */
    protected function _getCostSum()
    {
        if ($this->getAddSumType() == 'abs') {
            $oCur = $this->getConfig()->getActShopCurrencyObject();
            $dPrice = $this->getAddSum() * $oCur->rate * $this->_getMultiplier();
        } else {
            $dPrice = $this->_dPrice / 100 * $this->getAddSum();
        }

        return $dPrice;
    }
}
