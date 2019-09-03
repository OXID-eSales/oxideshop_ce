<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Order delivery manager.
 * Currently calculates price/costs.
 *
 */
class Delivery extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
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
     * @var \OxidEsales\Eshop\Core\Price
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
    protected static $_aProductList = [];

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
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = "select oxobjectid from oxobject2delivery 
                where oxdeliveryid = :oxdeliveryid and oxtype = :oxtype";
            $aArtIds = $oDb->getCol($sQ, [
                ':oxdeliveryid' => $this->getId(),
                ':oxtype' => 'oxarticles'
            ]);
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
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = "select oxobjectid from oxobject2delivery 
                where oxdeliveryid = :oxdeliveryid and oxtype = :oxtype";
            $aCatIds = $oDb->getCol($sQ, [
                ':oxdeliveryid' => $this->getId(),
                ':oxtype' => 'oxcategories'
            ]);
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
     * @param \OxidEsales\Eshop\Application\Model\BasketItem $oBasketItem basket item object
     *
     * @return double
     */
    public function getDeliveryAmount($oBasketItem)
    {
        $dAmount = 0;
        $oProduct = $oBasketItem->getArticle(false);

        if ($oProduct->isOrderArticle()) {
            $oProduct = $oProduct->getArticle();
        }

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
     * @param \OxidEsales\Eshop\Core\Price $oPrice delivery price to set
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
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function getDeliveryPrice($dVat = null)
    {
        if ($this->_oPrice === null) {
            // loading oxPrice object for final price calculation
            $oPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);
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

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "delete from `oxobject2delivery` where `oxobject2delivery`.`oxdeliveryid` = :oxdeliveryid";
        $oDb->execute($sQ, [
            ':oxdeliveryid' => $sOxId
        ]);

        return parent::delete($sOxId);
    }

    /**
     * Checks if delivery fits for current basket
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket shop basket
     *
     * @return bool
     */
    public function isForBasket($oBasket)
    {
        // amount for conditional check
        $blHasArticles = $this->hasArticles();
        $blHasCategories = $this->hasCategories();
        $blUse = true;
        $aggregatedDeliveryAmount = 0;
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
                    $artAmount = $this->getDeliveryAmount($oContent);
                    if ($this->isDeliveryRuleFitByArticle($artAmount)) {
                        $blForBasket = true;
                        $this->updateItemCount($oContent);
                        $this->increaseProductCount();
                    }
                    if (!$blForBasket) {
                        $aggregatedDeliveryAmount += $artAmount;
                    }
                } elseif ($blHasCategories) {
                    if (isset(self::$_aProductList[$sProductId])) {
                        $oProduct = self::$_aProductList[$sProductId];
                    } else {
                        $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                        $oProduct->setSkipAssign(true);

                        if (!$oProduct->load($sProductId)) {
                            continue;
                        }

                        $oProduct->setId($sProductId);
                        self::$_aProductList[$sProductId] = $oProduct;
                    }

                    foreach ($aDeliveryCategories as $sCatId) {
                        if ($oProduct->inCategory($sCatId)) {
                            $artAmount = $this->getDeliveryAmount($oContent);

                            $blUse = true;
                            if ($this->isDeliveryRuleFitByArticle($artAmount)) {
                                $blForBasket = true;
                                $this->updateItemCount($oContent);
                                $this->increaseProductCount();
                            }
                            if (!$blForBasket) {
                                $aggregatedDeliveryAmount += $artAmount;
                            }

                            //HR#5650 product might be in multiple rule categories, counting it once is enough
                            break;
                        }
                    }
                }
            }
        } else {
            // regular amounts check
            foreach ($oBasket->getContents() as $oContent) {
                $artAmount = $this->getDeliveryAmount($oContent);
                if ($this->isDeliveryRuleFitByArticle($artAmount)) {
                    $blForBasket = true;
                    $this->updateItemCount($oContent);
                    $this->increaseProductCount();
                }
                if (!$blForBasket) {
                    $aggregatedDeliveryAmount += $artAmount;
                }
            }
        }

        //#M1130: Single article in Basket, checked as free shipping, is not buyable (step 3 no payments found)
        if (!$blForBasket && $blUse && ($this->_checkDeliveryAmount($aggregatedDeliveryAmount) || $this->_blFreeShipping)) {
            $blForBasket = true;
        }

        return $blForBasket;
    }
    /**
     * Checks if delivery fits for one article
     *
     * @deprecated in b-dev since (2015-07-27), use isDeliveryRuleFitByArticle instead
     *
     * @param object  $content   shop basket item
     * @param integer $artAmount product amount
     *
     * @return bool
     */
    protected function _isForArticle($content, $artAmount)
    {
        return $this->isDeliveryRuleFitByArticle($artAmount);
    }

    /**
     * Update total count of product items are covered by current delivery.
     *
     * @param \OxidEsales\Eshop\Application\Model\BasketItem $content
     */
    protected function updateItemCount($content)
    {
        $this->_iItemCnt += $content->getAmount();
    }

    /**
     * Increase count of products which are covered by current delivery.
     */
    protected function increaseProductCount()
    {
        $this->_iProdCnt += 1;
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
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "SELECT `oxid` FROM `" . getViewName('oxdelivery') . "` 
            WHERE `oxtitle` = :oxtitle";
        $sId = $oDb->getOne($sQ, [
            ':oxtitle' => $sTitle
        ]);

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
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $this->_aCountriesISO = [];

            $sSelect = "
                SELECT
                    `oxcountry`.`oxisoalpha2`
                FROM `oxcountry`
                    LEFT JOIN `oxobject2delivery` ON `oxobject2delivery`.`oxobjectid` = `oxcountry`.`oxid`
                WHERE `oxobject2delivery`.`oxdeliveryid` = :oxdeliveryid
                    AND `oxobject2delivery`.`oxtype` = :oxtype";

            $rs = $oDb->getCol($sSelect, [
                ':oxdeliveryid' => $this->getId(),
                ':oxtype' => 'oxcountry'
            ]);
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

    /**
     * Checks if delivery rule applies for basket because of one article's amount.
     * Delivery rules that are to be applied once per cart can be ruled out here.
     *
     * @param integer $artAmount product amount
     *
     * @return bool
     */
    protected function isDeliveryRuleFitByArticle($artAmount)
    {
        $result = false;
        if ($this->getCalculationRule() != self::CALCULATION_RULE_ONCE_PER_CART) {
            if (!$this->_blFreeShipping && $this->_checkDeliveryAmount($artAmount)) {
                $result = true;
            }
        }

        return $result;
    }
}
