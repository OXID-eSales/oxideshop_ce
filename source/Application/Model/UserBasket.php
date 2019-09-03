<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxField;
use oxDb;

/**
 * Virtual basket manager class. Virtual baskets are user article lists which are stored in database (noticelists, wishlists).
 * The name of the class is left like this because of historic reasons.
 * It is more relevant to wishlist and noticelist than to shoping basket.
 * Collects shopping basket information, updates it (DB level), removes or adds
 * articles to it.
 *
 */
class UserBasket extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Array of fields which must be skipped when updating object data
     *
     * @var array
     */
    protected $_aSkipSaveFields = ['oxcreate', 'oxtimestamp'];

    /**
     * Current object class name
     *
     * @var string
     */
    protected $_sClassName = 'oxUserbasket';

    /**
     * Array of basket items
     *
     * @var array
     */
    protected $_aBasketItems = null;

    /**
     * Marker if basket is newly created. This avoids empty basket storing to DB
     *
     * @var bool
     */
    protected $_blNewBasket = false;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxuserbaskets');
    }

    /**
     * Inserts object data to DB, returns true on success.
     *
     * @return mixed
     */
    protected function _insert()
    {
        // marking basket as not new any more
        $this->_blNewBasket = false;

        if (!isset($this->oxuserbaskets__oxpublic->value)) {
            $this->oxuserbaskets__oxpublic = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);
        }

        $iTime = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $this->oxuserbaskets__oxupdate = new \OxidEsales\Eshop\Core\Field($iTime);

        return parent::_insert();
    }

    /**
     * Sets basket as newly created. This usually means that it is not
     * yet stored in DB and will only be stored if some item is added
     */
    public function setIsNewBasket()
    {
        $this->_blNewBasket = true;
        $iTime = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
        $this->oxuserbaskets__oxupdate = new \OxidEsales\Eshop\Core\Field($iTime);
    }

    /**
     * Checks if user basket is newly created
     *
     * @return bool
     */
    public function isNewBasket()
    {
        return $this->_blNewBasket;
    }

    /**
     * Checks if user basket is empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        if ($this->isNewBasket() || $this->getItemCount() < 1) {
            return true;
        }

        return false;
    }

    /**
     * Returns an array of articles belonging to the Items in the basket
     *
     * @return array of oxArticle
     */
    public function getArticles()
    {
        $aRes = [];
        $aItems = $this->getItems();
        if (is_array($aItems)) {
            foreach ($aItems as $sId => $oItem) {
                $oArticle = $oItem->getArticle($sId);
                $aRes[$this->_getItemKey($oArticle->getId(), $oItem->getSelList(), $oItem->getPersParams())] = $oArticle;
            }
        }

        return $aRes;
    }

    /**
     * Returns list of basket items
     *
     * @param bool $blReload      if TRUE forces to reload list
     * @param bool $blActiveCheck should articles be checked for active state?
     *
     * @return array of oxUserBasketItems
     */
    public function getItems($blReload = false, $blActiveCheck = true)
    {
        // cached ?
        if ($this->_aBasketItems !== null && !$blReload) {
            return $this->_aBasketItems;
        }

        // initializing
        $this->_aBasketItems = [];

        // loading basket items
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $sViewName = $oArticle->getViewName();

        $sSelect = "select oxuserbasketitems.* from oxuserbasketitems 
            left join $sViewName on oxuserbasketitems.oxartid = $sViewName.oxid ";
        if ($blActiveCheck) {
            $sSelect .= 'and ' . $oArticle->getSqlActiveSnippet() . ' ';
        }
        $sSelect .= "where oxuserbasketitems.oxbasketid = :oxbasketid and $sViewName.oxid is not null ";

        $sSelect .= " order by oxartnum, oxsellist, oxpersparam ";

        $oItems = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oItems->init('oxuserbasketitem');
        $oItems->selectstring($sSelect, [
            ':oxbasketid' => $this->getId()
        ]);

        foreach ($oItems as $oItem) {
            $sKey = $this->_getItemKey($oItem->oxuserbasketitems__oxartid->value, $oItem->getSelList(), $oItem->getPersParams());
            $this->_aBasketItems[$sKey] = $oItem;
        }

        return $this->_aBasketItems;
    }

    /**
     * Creates and returns  oxuserbasketitem object
     *
     * @param string $sProductId  Product Id
     * @param array  $aSelList    product select lists
     * @param string $aPersParams persistent parameters
     *
     * @return oxUserBasketItem
     */
    protected function _createItem($sProductId, $aSelList = null, $aPersParams = null)
    {
        $oNewItem = oxNew(\OxidEsales\Eshop\Application\Model\UserBasketItem::class);
        $oNewItem->oxuserbasketitems__oxartid = new \OxidEsales\Eshop\Core\Field($sProductId, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oNewItem->oxuserbasketitems__oxbasketid = new \OxidEsales\Eshop\Core\Field($this->getId(), \OxidEsales\Eshop\Core\Field::T_RAW);
        if ($aPersParams && count($aPersParams)) {
            $oNewItem->setPersParams($aPersParams);
        }

        if (!$aSelList) {
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oArticle->load($sProductId);
            $aSelectLists = $oArticle->getSelectLists();
            if (($iSelCnt = count($aSelectLists))) {
                $aSelList = array_fill(0, $iSelCnt, '0');
            }
        }

        $oNewItem->setSelList($aSelList);

        return $oNewItem;
    }


    /**
     * Searches for item in basket items array and returns it. If not item was
     * found - new item is created.
     *
     * @param string $sProductId  product id, basket item id or basket item index
     * @param array  $aSelList    select lists
     * @param string $aPersParams persistent parameters
     *
     * @return oxUserBasketItem
     */
    public function getItem($sProductId, $aSelList, $aPersParams = null)
    {
        // loading basket item list
        $aItems = $this->getItems();
        $sItemKey = $this->_getItemKey($sProductId, $aSelList, $aPersParams);
        $oItem = null;
        // returning existing item
        if (isset($aItems[$sProductId])) {
            $oItem = $aItems[$sProductId];
        } elseif (isset($aItems[$sItemKey])) {
            $oItem = $aItems[$sItemKey];
        } else {
            $oItem = $this->_createItem($sProductId, $aSelList, $aPersParams);
        }

        return $oItem;
    }

    /**
     * Returns unique item key according to its ID and user chosen select
     *
     * @param string $sProductId Product Id
     * @param array  $aSel       product select lists
     * @param array  $aPersParam basket item persistent parameters
     *
     * @return string
     */
    protected function _getItemKey($sProductId, $aSel = null, $aPersParam = null)
    {
        $aSel = ($aSel != null) ? $aSel : [0 => '0'];

        return md5($sProductId . '|' . serialize($aSel) . '|' . serialize($aPersParam));
    }

    /**
     * Returns current basket item count
     *
     * @param bool $blReload if TRUE forces to reload list
     *
     * @return int
     */
    public function getItemCount($blReload = false)
    {
        return count($this->getItems($blReload));
    }

    /**
     * Method adds/removes user chosen article to/from his noticelist or wishlist. Returns total amount
     * of articles in list.
     *
     * @param string $sProductId Article ID
     * @param double $dAmount    Product amount
     * @param array  $aSel       product select lists
     * @param bool   $blOverride if true overrides $dAmount, else sums previous with current it
     * @param array  $aPersParam product persistent parameters (default null)
     *
     * @return integer
     */
    public function addItemToBasket($sProductId = null, $dAmount = null, $aSel = null, $blOverride = false, $aPersParam = null)
    {
        // basket info is only written in DB when something is in it
        if ($this->_blNewBasket) {
            $this->save();
        }

        if (($oUserBasketItem = $this->getItem($sProductId, $aSel, $aPersParam))) {
            // updating object info and adding (if not yet added) item into basket items array
            if (!$blOverride && !empty($oUserBasketItem->oxuserbasketitems__oxamount->value)) {
                $dAmount += $oUserBasketItem->oxuserbasketitems__oxamount->value;
            }

            if (!$dAmount) {
                // amount = 0 removes the item
                $oUserBasketItem->delete();
                if (isset($this->_aBasketItems[$this->_getItemKey($sProductId, $aSel, $aPersParam)])) {
                    unset($this->_aBasketItems[$this->_getItemKey($sProductId, $aSel, $aPersParam)]);
                }
            } else {
                $oUserBasketItem->oxuserbasketitems__oxamount = new \OxidEsales\Eshop\Core\Field($dAmount, \OxidEsales\Eshop\Core\Field::T_RAW);
                $oUserBasketItem->save();

                $this->_aBasketItems[$this->_getItemKey($sProductId, $aSel, $aPersParam)] = $oUserBasketItem;
            }

            //update timestamp
            $this->oxuserbaskets__oxupdate = new \OxidEsales\Eshop\Core\Field(\OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime());
            $this->save();

            return $dAmount;
        }
    }

    /**
     * Deletes current basket history
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

        $blDelete = false;
        if ($sOXID && ($blDelete = parent::delete($sOXID))) {
            // cleaning up related data
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = "delete from oxuserbasketitems where oxbasketid = :oxbasketid";
            $oDb->execute($sQ, [
                ':oxbasketid' => $sOXID
            ]);
            $this->_aBasketItems = null;
        }

        return $blDelete;
    }

    /**
     * Checks if user basket is visible for current user (public or own basket)
     *
     * @return bool
     */
    public function isVisible()
    {
        $oActivUser = $this->getConfig()->getUser();
        $sActivUserId = null;
        if ($oActivUser) {
            $sActivUserId = $oActivUser->getId();
        }

        $blIsVisible = (bool) ($this->oxuserbaskets__oxpublic->value) ||
                       ($sActivUserId && ($this->oxuserbaskets__oxuserid->value == $sActivUserId));

        return $blIsVisible;
    }
}
