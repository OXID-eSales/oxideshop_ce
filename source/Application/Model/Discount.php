<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\Exception\StandardException;
use stdClass;

/**
 * Discounts manager.
 *
 */
class Discount extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxdiscount';

    /**
     * Stores amount of articles which are applied for current discount
     *
     * @var double
     */
    protected $_dAmount = null;

    /**
     * Basket ident
     *
     * @var string
     */
    protected $_sBasketIdent = null;

    /**
     * Is discount for article or For category
     *
     * @var bool
     */
    protected $_blIsForArticleOrForCategory = null;

    /**
     * Is discount set for article, array index article id
     *
     * @var array
     */
    protected $_aHasArticleDiscounts = [];

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxdiscount');
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOXID Object ID(default null)
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        if (!$sOXID) {
            $sOXID = $this->getId();
        }
        if (!$sOXID) {
            return false;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $oDb->execute('delete from oxobject2discount where oxobject2discount.oxdiscountid = :oxdiscountid', [
            ':oxdiscountid' => $sOXID
        ]);

        return parent::delete($sOXID);
    }

    /**
     * Save the discount.
     * Assigns a value to oxsort, if it was null
     * Does input validation before saving the discount.
     *
     * Returns saving status
     *
     * @throws \oxInputException
     * @throws StandardException
     *
     * @return bool
     */
    public function save()
    {
        // Auto assign oxsort, if it is null
        $oxsort = $this->oxdiscount__oxsort->value;
        if (is_null($oxsort)) {
            $shopId = $this->oxdiscount__oxshopid->value;
            $newSort = $this->getNextOxsort($shopId);
            $this->oxdiscount__oxsort = new \oxField($newSort, \OxidEsales\Eshop\Core\Field::T_RAW);
        }

        // Validate oxsort before saving
        if (!is_numeric($this->oxdiscount__oxsort->value)) {
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
            $exception->setMessage('DISCOUNT_ERROR_OXSORT_NOT_A_NUMBER');

            throw $exception;
        }

        try {
            $saveStatus = parent::save();
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            if ($exception->getCode() == \OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database::DUPLICATE_KEY_ERROR_CODE && false !== strpos($exception->getMessage(), 'UNIQ_OXSORT')) {
                $exception = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
                $exception->setMessage('DISCOUNT_ERROR_OXSORT_NOT_UNIQUE');
            }

            throw $exception;
        }

        return $saveStatus;
    }
    /**
     * Check for global discount (no articles, no categories)
     *
     * @return bool
     */
    public function isGlobalDiscount()
    {
        if (is_null($this->_blIsForArticleOrForCategory)) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

            $sQuery = "select 1
                        from oxobject2discount
                        where oxdiscountid = :oxdiscountid and (oxtype = :oxtypearticles or oxtype = :oxtypecategories)";
            $params = [
                ':oxdiscountid' => $this->oxdiscount__oxid->value,
                ':oxtypearticles' => 'oxarticles',
                ':oxtypecategories' => 'oxcategories'
            ];

            $this->_blIsForArticleOrForCategory = $oDb->getOne($sQuery, $params) ? false : true;
        }

        return $this->_blIsForArticleOrForCategory;
    }

    /**
     * Checks if discount applies for article
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article object
     *
     * @return bool
     */
    public function isForArticle($oArticle)
    {
        // item discounts may only be applied for basket
        if ($this->oxdiscount__oxaddsumtype->value == 'itm') {
            return false;
        }

        if ($this->oxdiscount__oxamount->value || $this->oxdiscount__oxprice->value) {
            return false;
        }

        if ($this->oxdiscount__oxpriceto->value && ($this->oxdiscount__oxpriceto->value < $oArticle->getBasePrice())) {
            return false;
        }

        if ($this->isGlobalDiscount()) {
            return true;
        }

        $sArticleId = $oArticle->getProductId();

        if (!isset($this->_aHasArticleDiscounts[$sArticleId])) {
            $blResult = $this->_isArticleAssigned($oArticle) || $this->_isCategoriesAssigned($oArticle->getCategoryIds());

            $this->_aHasArticleDiscounts[$sArticleId] = $blResult;
        }

        return $this->_aHasArticleDiscounts[$sArticleId];
    }

    /**
     * Checks if discount is setup for some basket item
     *
     * @param object $oArticle basket item
     *
     * @return bool
     */
    public function isForBasketItem($oArticle)
    {
        if ($this->oxdiscount__oxamount->value == 0 && $this->oxdiscount__oxprice->value == 0) {
            return false;
        }

        // skipping bundle discounts
        if ($this->oxdiscount__oxaddsumtype->value == 'itm') {
            return false;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        // check if this article is assigned
        $sQ = "select 1 from oxobject2discount 
            where oxdiscountid = :oxdiscountid and oxtype = :oxtype ";
        $sQ .= $this->_getProductCheckQuery($oArticle);
        $params = [
            ':oxdiscountid' => $this->oxdiscount__oxid->value,
            ':oxtype' => 'oxarticles'
        ];

        if (!($blOk = ( bool )$oDb->getOne($sQ, $params))) {
            // checking article category
            $blOk = $this->_checkForArticleCategories($oArticle);
        }

        return $blOk;
    }

    /**
     * Tests if total amount or price (price priority) of articles that can be applied to current discount fits to discount configuration
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket basket
     *
     * @return bool
     */
    public function isForBasketAmount($oBasket)
    {
        $dAmount = 0;
        $aBasketItems = $oBasket->getContents();
        foreach ($aBasketItems as $oBasketItem) {
            $oBasketArticle = $oBasketItem->getArticle(false);

            if ($this->oxdiscount__oxaddsumtype->value != 'itm') {
                $blForBasketItem = $this->isForBasketItem($oBasketArticle);
            } else {
                $blForBasketItem = $this->isForBundleItem($oBasketArticle);
            }

            if ($blForBasketItem) {
                $dRate = $oBasket->getBasketCurrency()->rate;
                if ($this->oxdiscount__oxprice->value) {
                    if (($oPrice = $oBasketArticle->getPrice())) {
                        $dAmount += ($oPrice->getPrice() * $oBasketItem->getAmount()) / $dRate;
                    }
                } elseif ($this->oxdiscount__oxamount->value) {
                    $dAmount += $oBasketItem->getAmount();
                }
            }
        }

        return $this->isForAmount($dAmount);
    }

    /**
     * Tests if passed amount or price fits current discount (price priority)
     *
     * @param double $dAmount amount or price to check (price priority)
     *
     * @return bool
     */
    public function isForAmount($dAmount)
    {
        $blIs = true;

        if ($this->oxdiscount__oxprice->value &&
            ($dAmount < $this->oxdiscount__oxprice->value || $dAmount > $this->oxdiscount__oxpriceto->value)
        ) {
            $blIs = false;
        } elseif ($this->oxdiscount__oxamount->value &&
                  ($dAmount < $this->oxdiscount__oxamount->value || $dAmount > $this->oxdiscount__oxamountto->value)
        ) {
            $blIs = false;
        }

        return $blIs;
    }

    /**
     * Checks if discount is setup for whole basket
     *
     * @param object $oBasket basket object
     *
     * @return bool
     */
    public function isForBasket($oBasket)
    {
        // initial configuration check
        if ($this->oxdiscount__oxamount->value == 0 && $this->oxdiscount__oxprice->value == 0) {
            return false;
        }

        $oSummary = $oBasket->getBasketSummary();
        // amounts check
        if ($this->oxdiscount__oxamount->value && ($oSummary->iArticleCount < $this->oxdiscount__oxamount->value || $oSummary->iArticleCount > $this->oxdiscount__oxamountto->value)) {
            return false;
        // price check
        } elseif ($this->oxdiscount__oxprice->value) {
            $dRate = $oBasket->getBasketCurrency()->rate;
            if ($oSummary->dArticleDiscountablePrice < $this->oxdiscount__oxprice->value * $dRate || $oSummary->dArticleDiscountablePrice > $this->oxdiscount__oxpriceto->value * $dRate) {
                return false;
            }
        }

        // oxobject2discount configuration check
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = 'select 1 from oxobject2discount 
            where oxdiscountid = :oxdiscountid and oxtype in ("oxarticles", "oxcategories" ) ';
        $params = [
            ':oxdiscountid' => $this->oxdiscount__oxid->value
        ];

        return !((bool)$oDb->getOne($sQ, $params));
    }

    /**
     * Checks if discount type is bundle discount
     *
     * @param object $oArticle article object
     *
     * @return bool
     */
    public function isForBundleItem($oArticle)
    {
        if ($this->oxdiscount__oxaddsumtype->value != 'itm') {
            return false;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select 1 from oxobject2discount where oxdiscountid = :oxdiscountid";
        $sQ .= $this->_getProductCheckQuery($oArticle);
        $params = [
            ':oxdiscountid' => $this->getId()
        ];

        if (!($blOk = (bool)$oDb->getOne($sQ, $params))) {
            // additional checks for amounts and other dependencies
            $blOk = $this->_checkForArticleCategories($oArticle);
        }

        return $blOk;
    }

    /**
     * Checks if discount type is whole basket bundle discount
     *
     * @param object $oBasket basket object
     *
     * @return bool
     */
    public function isForBundleBasket($oBasket)
    {
        if ($this->oxdiscount__oxaddsumtype->value != 'itm') {
            return false;
        }

        return $this->isForBasket($oBasket);
    }

    /**
     * Returns absolute discount value
     *
     * @param float     $dPrice  item price
     * @param float|int $dAmount item amount, interpretted only when discount is absolute (default 1)
     *
     * @return float
     */
    public function getAbsValue($dPrice, $dAmount = 1)
    {
        if ($this->oxdiscount__oxaddsumtype->value == '%') {
            return $dPrice * ($this->oxdiscount__oxaddsum->value / 100);
        } else {
            $oCur = $this->getConfig()->getActShopCurrencyObject();

            return $this->oxdiscount__oxaddsum->value * $dAmount * $oCur->rate;
        }
    }

    /**
     * Return discount percent
     *
     * @param double $dPrice - price from which calculates discount
     *
     * @return double
     */
    public function getPercentage($dPrice)
    {
        if ($this->getAddSumType() == 'abs' && $dPrice > 0) {
            return $this->getAddSum() / $dPrice * 100;
        } else {
            return $this->getAddSum();
        }
    }

    /**
     * Return add sum in abs type discount with efected currency rate;
     * Return discount percent value in other way;
     *
     * @return double
     */
    public function getAddSum()
    {
        if ($this->oxdiscount__oxaddsumtype->value == 'abs') {
            $oCur = $this->getConfig()->getActShopCurrencyObject();

            return $this->oxdiscount__oxaddsum->value * $oCur->rate;
        } else {
            return $this->oxdiscount__oxaddsum->value;
        }
    }

    /**
     * Return addsum type
     *
     * @return string
     */
    public function getAddSumType()
    {
        return $this->oxdiscount__oxaddsumtype->value;
    }

    /**
     * Returns amount of items to bundle
     *
     * @param double $dAmount item amount
     *
     * @return double
     */
    public function getBundleAmount($dAmount)
    {
        $dItemAmount = $this->oxdiscount__oxitmamount->value;

        // Multiplying bundled articles count, if allowed
        if ($this->oxdiscount__oxitmmultiple->value && $this->oxdiscount__oxamount->value > 0) {
            $dItemAmount = floor($dAmount / $this->oxdiscount__oxamount->value) * $this->oxdiscount__oxitmamount->value;
        }

        return $dItemAmount;
    }

    /**
     * Returns compact discount object which is used in oxbasket
     *
     * @return stdClass
     */
    public function getSimpleDiscount()
    {
        $oDiscount = new stdClass();
        $oDiscount->sOXID = $this->getId();
        $oDiscount->sDiscount = $this->oxdiscount__oxtitle->value;
        $oDiscount->sType = $this->oxdiscount__oxaddsumtype->value;

        return $oDiscount;
    }

    /**
     * Returns article ids assigned to discount
     *
     * @return array
     */
    public function getArticleIds()
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $params = [
            ':oxdiscountid' => $this->getId(),
            ':oxtype' => 'oxarticles'
        ];

        return $db->getCol("select `oxobjectid` from oxobject2discount 
            where oxdiscountid = :oxdiscountid and oxtype = :oxtype", $params);
    }

    /**
     * Returns category ids asigned to discount
     *
     * @return array
     */
    public function getCategoryIds()
    {
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $params = [
            ':oxdiscountid' => $this->getId(),
            ':oxtype' => 'oxcategories'
        ];

        return $db->getCol("select `oxobjectid` from oxobject2discount 
            where oxdiscountid = :oxdiscountid and oxtype = :oxtype", $params);
    }

    /**
     * Increment the maximum value of oxsort found in the database by certain amount and return it.
     *
     * @param int $shopId The id of the current shop
     *
     * @return int The incremented oxsort
     */
    public function getNextOxsort($shopId)
    {
        $query = "SELECT MAX(`oxsort`)+10 FROM `oxdiscount` WHERE `oxshopid` = :oxshopid";
        $nextSort = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($query, [
            ':oxshopid' => $shopId
        ]);

        return (int) $nextSort;
    }

    /**
     * Checks if discount may be applied according amounts info
     *
     * @param object $oArticle article object to chesk
     *
     * @return bool
     */
    protected function _checkForArticleCategories($oArticle)
    {
        // check if article is in some assigned category
        $aCatIds = $oArticle->getCategoryIds();
        if (!$aCatIds || !count($aCatIds)) {
            // no categories are set for article, so no discounts from categories..
            return false;
        }

        $sCatIds = "(" . implode(",", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aCatIds)) . ")";

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        // getOne appends limit 1, so this one should be fast enough
        $sQ = "select oxobjectid from oxobject2discount 
            where oxdiscountid = :oxdiscountid 
                and oxobjectid in $sCatIds 
                and oxtype = :oxtype";

        return $oDb->getOne($sQ, [
            ':oxdiscountid' => $this->oxdiscount__oxid->value,
            ':oxtype' => 'oxcategories'
        ]);
    }

    /**
     * Returns part of query for discount check. If product is variant - query contains both id check e.g.
     * "and (oxobjectid = '...' or oxobjectid = '...')
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oProduct product used for discount check
     *
     * @return string
     */
    protected function _getProductCheckQuery($oProduct)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        // check if this article is assigned
        if (($sParentId = $oProduct->getParentId())) {
            $sArticleId = " and ( oxobjectid = " . $oDb->quote($oProduct->getProductId()) . " or oxobjectid = " . $oDb->quote($sParentId) . " )";
        } else {
            $sArticleId = " and oxobjectid = " . $oDb->quote($oProduct->getProductId());
        }

        return $sArticleId;
    }

    /**
     * Checks whether this article is assigned to discount
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle
     *
     * @return bool
     */
    protected function _isArticleAssigned($oArticle)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sQ = "select 1
                from oxobject2discount
                where oxdiscountid = :oxdiscountid 
                    and oxtype = :oxtype ";
        $sQ .= $this->_getProductCheckQuery($oArticle);
        $params = [
            ':oxdiscountid' => $this->oxdiscount__oxid->value,
            ':oxtype' => 'oxarticles'
        ];

        return $oDb->getOne($sQ, $params) ? true : false;
    }

    /**
     * Checks whether categories are assigned to discount
     *
     * @param array $aCategoryIds
     *
     * @return bool
     */
    protected function _isCategoriesAssigned($aCategoryIds)
    {
        if (empty($aCategoryIds)) {
            return false;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sCategoryIds = "(" . implode(",", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aCategoryIds)) . ")";
        $sQ = "select 1
                from oxobject2discount
                where oxdiscountid = :oxdiscountid and oxobjectid in {$sCategoryIds} and oxtype = :oxtype";
        $params = [
            ':oxdiscountid' => $this->oxdiscount__oxid->value,
            ':oxtype' => 'oxcategories'
        ];

        return $oDb->getOne($sQ, $params) ? true : false;
    }
}
