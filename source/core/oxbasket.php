<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

/**
 * @package core
 */
class oxBasket extends oxSuperCfg
{
    /**
     * Array or oxbasketitem objects
     *
     * @var array
     */
    protected $_aBasketContents = array();

    /**
     * Number of different product type in basket
     *
     * @var int
     */
    protected $_iProductsCnt = 0;

    /**
     * Number of basket items
     *
     * @var double
     */
    protected $_dItemsCnt = 0.0;

    /**
     * Basket weight
     *
     * @var double
     */
    protected $_dWeight = 0.0;

    /**
     * Total basket price
     *
     * @var oxPrice
     */
    protected $_oPrice = null;

    /**
     * The list of all basket item prices
     *
     * @var oxPriceList
     */
    protected $_oProductsPriceList = null;

    /**
     * Basket discounts information
     *
     * @var array
     */
    protected $_aDiscounts = array();

    /**
     * Basket items discounts information
     *
     * @var array
     */
    protected $_aItemDiscounts = array();

    /**
     * Basket order ID. Usually this ID is set on last order step
     *
     * @var string
     */
    protected $_sOrderId = null;

    /**
     * Array of vouchers applied on basket price
     *
     * @var array
     */
    protected $_aVouchers = array();

    /**
     * Additional costs array of oxPrice objects
     *
     * @var array
     */
    protected $_aCosts = array();

    /**
     * Sum price of articles applicapable to discounts
     *
     * @var oxPrice
     */
    protected $_oDiscountProductsPriceList = null;

    /**
     * Sum price of articles not applicapable to discounts
     *
     * @var oxPrice
     */
    protected $_oNotDiscountedProductsPriceList = null;

    /**
     * Basket recalculation marker
     *
     * @var bool
     */
    protected $_blUpdateNeeded = true;

    /**
     * oxbasket summary object, usually used for discount calcualtions etc
     *
     * @var array
     */
    protected $_aBasketSummary = null;

    /**
     * Basket Payment ID
     *
     * @var string
     */
    protected $_sPaymentId = null;

    /**
     * Basket Shipping set ID
     *
     * @var string
     */
    protected $_sShippingSetId = null;

    /**
     * Ref. to session user
     *
     * @var oxuser
     */
    protected $_oUser = null;

    /**
     * Total basket products discount oxprice object (does not include voucher discount)
     *
     * @var oxprice
     */
    protected $_oTotalDiscount = null;

    /**
     * Basket voucher discount oxprice object
     *
     * @var oxprice
     */
    protected $_oVoucherDiscount = null;

    /**
     * Basket currency
     *
     * @var object
     */
    protected $_oCurrency = null;

    /**
     * Skip or not vouchers availability checking
     *
     * @var bool
     */
    protected $_blSkipVouchersAvailabilityChecking = null;

    /**
     * Netto price including discount and voucher
     *
     * @var double
     */
    protected $_dDiscountedProductNettoPrice = null;

    /**
     * All VAT values with discount and voucher
     *
     * @var array
     */
    protected $_aDiscountedVats = null;

    /**
     * Skip discounts marker
     *
     * @var boolean
     */
    protected $_blSkipDiscounts = false;

    /**
     * User set delivery costs
     *
     * @var array
     */
    protected $_oDeliveryPrice = null;

    /**
     * Basket product stock check (live db check) status
     *
     * @var bool
     */
     protected $_blCheckStock = true;

    /**
     * discount calculation marker
     *
     * @var bool
     */
    protected $_blCalcDiscounts = true;

    /**
     * Basket category id
     *
     * @var string
     */
    protected $_sBasketCategoryId = null;

    /**
     * Category change warning state
     *
     * @var bool
     */
    protected $_blShowCatChangeWarning = false;

    /**
     * Trusted shops protection product ID
     *
     * @var string
     */
    protected $_sTsProductId = null;

    /**
     * new basket item addition state
     * @var bool
     */
    protected $_blNewITemAdded = null;

    /**
     * if basket has downloadable product
     * @var bool
     */
    protected $_blDownloadableProducts = null;

    /**
     * Checks if configuration allows basket usage or if user agent is search engine
     *
     * @return bool
     */
    public function isEnabled()
    {
        return !oxUtils::getInstance()->isSearchEngine();
    }

    /**
     * change old key to new one but retain key position in array
     *
     * @param string $sOldKey old key
     * @param string $sNewKey new key to place in old one's place
     * @param mixed  $value   (optional)
     *
     * @return null
     */
    protected function _changeBasketItemKey($sOldKey, $sNewKey, $value = null)
    {
        reset($this->_aBasketContents);
        $iOldKeyPlace = 0;
        while (key($this->_aBasketContents) != $sOldKey && next($this->_aBasketContents)) {
            ++$iOldKeyPlace;
        }
        $aNewCopy = array_merge(
            array_slice($this->_aBasketContents, 0, $iOldKeyPlace, true),
            array($sNewKey => $value),
            array_slice($this->_aBasketContents, $iOldKeyPlace+1, count($this->_aBasketContents)-$iOldKeyPlace, true)
        );
        $this->_aBasketContents = $aNewCopy;
    }

    /**
     * Adds user item to basket. Returns oxbasketitem object if adding succeded
     *
     * @param string $sProductID       id of product
     * @param double $dAmount          product amount
     * @param array  $aSel             product select lists (default null)
     * @param array  $aPersParam       product persistent parameters (default null)
     * @param bool   $blOverride       marker to acumulate passed amount or renew (default false)
     * @param bool   $blBundle         marker if product is bundle or not (default false)
     * @param string $sOldBasketItemId id if old basket item if to change it
     *
     * @throws oxOutOfStockException oxArticleInputException, oxNoArticleException
     *
     * @return object
     */
    public function addToBasket( $sProductID, $dAmount, $aSel = null, $aPersParam = null, $blOverride = false, $blBundle = false, $sOldBasketItemId = null )
    {
        // enabled ?
        if ( !$this->isEnabled() )
            return null;

        // basket exclude
        if ( $this->getConfig()->getConfigParam( 'blBasketExcludeEnabled' ) ) {
            if ( !$this->canAddProductToBasket( $sProductID ) ) {
                $this->setCatChangeWarningState( true );
                return null;
            } else {
                $this->setCatChangeWarningState( false );
            }
        }

        $sItemId = $this->getItemKey( $sProductID, $aSel, $aPersParam, $blBundle );
        if ( $sOldBasketItemId && ( strcmp( $sOldBasketItemId, $sItemId ) != 0 ) ) {
            if ( isset( $this->_aBasketContents[$sItemId] ) ) {
                // we are merging, so params will just go to the new key
                unset( $this->_aBasketContents[$sOldBasketItemId] );
                // do not override stock
                $blOverride = false;
            } else {
                // value is null - means isset will fail and real values will be filled
                $this->_changeBasketItemKey( $sOldBasketItemId, $sItemId );
            }
        }

        // after some checks item must be removed from basket
        $blRemoveItem = false;

        // initialting exception storage
        $oEx = null;

        if ( isset( $this->_aBasketContents[$sItemId] ) ) {

            //updating existing
            try {
                // setting stock check status
                $this->_aBasketContents[$sItemId]->setStockCheckStatus( $this->getStockCheckMode() );
                //validate amount
                //possibly throws exception
                $this->_aBasketContents[$sItemId]->setAmount( $dAmount, $blOverride, $sItemId );
            } catch( oxOutOfStockException $oEx ) {
                // rethrow later
            }

        } else {
            //inserting new
            $oBasketItem = oxNew( 'oxbasketitem' );
            try {
                $oBasketItem->setStockCheckStatus( $this->getStockCheckMode() );
                $oBasketItem->init( $sProductID, $dAmount, $aSel, $aPersParam, $blBundle );
            } catch( oxNoArticleException $oEx ) {
                // in this case that the article does not exist remove the item from the basket by setting its amount to 0
                //$oBasketItem->dAmount = 0;
                $blRemoveItem = true;

            } catch( oxOutOfStockException $oEx ) {
                // rethrow later
            } catch ( oxArticleInputException $oEx ) {
                // rethrow later
                $blRemoveItem = true;
            }

            $this->_aBasketContents[$sItemId] = $oBasketItem;
        }

        //in case amount is 0 removing item
        if ( $this->_aBasketContents[$sItemId]->getAmount() == 0 || $blRemoveItem ) {
            $this->removeItem( $sItemId );
        } elseif ( $blBundle ) {
            //marking bundles
            $this->_aBasketContents[$sItemId]->setBundle( true );
        }

        //calling update method
        $this->onUpdate();

        if ( $oEx ) {
            throw $oEx;
        }

        // notifying that new basket item was added
        if (!$blBundle) {
            $this->_addedNewItem( $sProductID, $dAmount, $aSel, $aPersParam, $blOverride, $blBundle, $sOldBasketItemId );
        }

        // returning basket item object
        return $this->_aBasketContents[$sItemId];
    }

    /**
     * Adds order article to basket (method normally used while recalculating order)
     *
     * @param oxorderarticle $oOrderArticle order article to store in basket
     *
     * @return oxbasketitem
     */
    public function addOrderArticleToBasket( $oOrderArticle )
    {
        // adding only if amount > 0
        if ( $oOrderArticle->oxorderarticles__oxamount->value > 0 && !$oOrderArticle->isBundle() ) {
            $sItemId = $oOrderArticle->getId();

            //inserting new
            $this->_aBasketContents[$sItemId] = oxNew( 'oxbasketitem' );
            $this->_aBasketContents[$sItemId]->initFromOrderArticle( $oOrderArticle );
            $this->_aBasketContents[$sItemId]->setWrapping( $oOrderArticle->oxorderarticles__oxwrapid->value );
            $this->_aBasketContents[$sItemId]->setBundle( $oOrderArticle->isBundle() );

            //calling update method
            $this->onUpdate();

            return $this->_aBasketContents[$sItemId];
        } elseif ( $oOrderArticle->isBundle() ) {
            // deleting bundles, they are handled automatically
            $oOrderArticle->delete();
        }
    }

    /**
     * Sets stock control mode
     *
     * @param bool $blCheck stock control mode
     *
     * @return null
     */
    public function setStockCheckMode( $blCheck )
    {
        $this->_blCheckStock = $blCheck;
    }

    /**
     * Returns stock control mode
     *
     * @return bool
     */
    public function getStockCheckMode()
    {
        return $this->_blCheckStock;
    }

    /**
     * Returns unique basket item identifier which consist from product ID,
     * select lists data, persistent info and bundle property
     *
     * @param string $sProductId       basket item id
     * @param array  $aSel             basket item selectlists
     * @param array  $aPersParam       basket item persistent parameters
     * @param bool   $blBundle         bundle marker
     * @param var    $sAdditionalParam possible additional information
     *
     * @return string
     */
    public function getItemKey( $sProductId, $aSel = null, $aPersParam = null, $blBundle = false, $sAdditionalParam = '' )
    {
        $aSel = ( $aSel != null) ? $aSel : array (0=>'0');

        $sItemKey = md5( $sProductId.'|'.serialize( $aSel ).'|'.serialize( $aPersParam ).'|'.( int ) $blBundle . '|' . serialize( $sAdditionalParam ) );

        return $sItemKey;
    }


    /**
     * Removes item from basket
     *
     * @param string $sItemKey basket item key
     *
     * @return null
     */
    public function removeItem( $sItemKey )
    {
        if ($this->getConfig()->getConfigParam( 'blPsBasketReservationEnabled' )) {
            if (isset($this->_aBasketContents[$sItemKey])) {
                $sArticleId = $this->_aBasketContents[$sItemKey]->getProductId();
                if ($sArticleId) {
                    $this->getSession()
                            ->getBasketReservations()
                            ->discardArticleReservation($sArticleId);
                }
            }
        }
        unset( $this->_aBasketContents[$sItemKey] );

        // basket exclude
        if ( !count($this->_aBasketContents) && $this->getConfig()->getConfigParam( 'blBasketExcludeEnabled' ) ) {
            $this->setBasketRootCatId(null);
        }
    }

    /**
     * Unsets bundled basket items from basket contents array
     *
     * @return null
     */
    protected function _clearBundles()
    {
        reset( $this->_aBasketContents );
        while ( list( $sItemKey, $oBasketItem ) = each( $this->_aBasketContents ) ) {
            if ( $oBasketItem->isBundle() ) {
                $this->removeItem( $sItemKey );
            }
        }
    }

    /**
     * Returns array of bundled articles IDs for basket item
     *
     * @param object $oBasketItem basket item object
     *
     * @return array
     */
    protected function _getArticleBundles( $oBasketItem )
    {
        $aBundles = array();

        if ( $oBasketItem->isBundle() ) {
            return $aBundles;
        }

        $oArticle = $oBasketItem->getArticle( true );
        if ( $oArticle && $oArticle->oxarticles__oxbundleid->value ) {
            $aBundles[$oArticle->oxarticles__oxbundleid->value] = 1;
        }

        return $aBundles;
    }

    /**
     * Returns array of bundled discount articles
     *
     * @param object $oBasketItem basket item object
     * @param array  $aBundles    array of found bundles
     *
     * @return array
     */
    protected function _getItemBundles( $oBasketItem, $aBundles = array() )
    {
        if ( $oBasketItem->isBundle() ) {
            return array();
        }

        // does this object still exists ?
        if ( $oArticle = $oBasketItem->getArticle( true ) ) {
            $aDiscounts = oxDiscountList::getInstance()->getBasketItemBundleDiscounts( $oArticle, $this, $this->getBasketUser() );

            foreach ( $aDiscounts as $oDiscount ) {

                $iAmnt = $oDiscount->getBundleAmount( $oBasketItem->getAmount() );
                if ( $iAmnt ) {
                    //init array element
                    if ( !isset( $aBundles[$oDiscount->oxdiscount__oxitmartid->value] ) ) {
                        $aBundles[$oDiscount->oxdiscount__oxitmartid->value] = 0;
                    }

                    if ($oDiscount->oxdiscount__oxitmmultiple->value) {
                        $aBundles[$oDiscount->oxdiscount__oxitmartid->value] += $iAmnt;
                    } else {
                        $aBundles[$oDiscount->oxdiscount__oxitmartid->value] = $iAmnt;
                    }
                }
            }
        }

        return $aBundles;
    }

    /**
     * Returns array of bundled discount articles for whole basket
     *
     * @param array $aBundles array of found bundles
     *
     * @return array
     */
    protected function _getBasketBundles( $aBundles = array() )
    {
        $aDiscounts = oxDiscountList::getInstance()->getBasketBundleDiscounts( $this, $this->getBasketUser() );

        // calculating amount of non bundled/discount items
        $dAmount = 0;
        foreach ( $this->_aBasketContents as $oBasketItem ) {
            if ( !( $oBasketItem->isBundle() || $oBasketItem->isDiscountArticle() ) ) {
                $dAmount += $oBasketItem->getAmount();
            }
        }

        foreach ( $aDiscounts as $oDiscount ) {
            if ($oDiscount->oxdiscount__oxitmartid->value) {
                if ( !isset( $aBundles[$oDiscount->oxdiscount__oxitmartid->value] ) ) {
                    $aBundles[$oDiscount->oxdiscount__oxitmartid->value] = 0;
                }

                $aBundles[$oDiscount->oxdiscount__oxitmartid->value] += $oDiscount->getBundleAmount( $dAmount );
            }
        }

        return $aBundles;
    }

    /**
     * Iterates through basket contents and adds bundles to items + adds
     * global basket bundles
     *
     * @return null
     */
    protected function _addBundles()
    {
        $aBundles = array();
        // iterating through articles and binding bundles
        foreach ( $this->_aBasketContents as $key => $oBasketItem ) {
            try {
                // adding discount type bundles
                if ( !$oBasketItem->isDiscountArticle() && !$oBasketItem->isBundle() ) {
                    $aBundles = $this->_getItemBundles( $oBasketItem, $aBundles );
                } else {
                    continue;
                }

                    // adding item type bundles
                    $aArtBundles = $this->_getArticleBundles( $oBasketItem );

                    // adding bundles to basket
                    $this->_addBundlesToBasket( $aArtBundles );
            } catch ( oxNoArticleException $oEx ) {
                $this->removeItem( $key );
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            } catch( oxArticleInputException $oEx ) {
                $this->removeItem( $key );
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
            }
        }

        // adding global basket bundles
        $aBundles = $this->_getBasketBundles( $aBundles );

        // adding all bundles to basket
        if ( $aBundles ) {
            $this->_addBundlesToBasket( $aBundles );
        }
    }

    /**
     * Adds bundles to basket
     *
     * @param array $aBundles added bundle articles
     *
     * @return null
     */
    protected function _addBundlesToBasket( $aBundles )
    {
        foreach ( $aBundles as $sBundleId => $dAmount ) {
            if ( $dAmount ) {
                try {
                    if ( $oBundleItem = $this->addToBasket( $sBundleId, $dAmount, null, null, false, true ) ) {
                        $oBundleItem->setAsDiscountArticle( true );
                    }
                } catch(oxArticleException $oEx) {
                    // caught and ignored
                }
            }
        }

    }

    /**
     * Iterates through basket items and calculates its prices and discounts
     *
     * @return null
     */
    protected function _calcItemsPrice()
    {
        // resetting
        $this->setSkipDiscounts( false );
        $this->_iProductsCnt = 0; // count different types
        $this->_dItemsCnt    = 0; // count of item units
        $this->_dWeight      = 0; // basket weight

        // resetting
        $this->_aItemDiscounts = array();

        $this->_oProductsPriceList = oxNew( 'oxpricelist' );
        $this->_oDiscountProductsPriceList = oxNew( 'oxpricelist' );
        $this->_oNotDiscountedProductsPriceList = oxNew( 'oxpricelist' );

        $oDiscountList = oxDiscountList::getInstance();

        foreach ( $this->_aBasketContents as $oBasketItem ) {
            $this->_iProductsCnt++;
            $this->_dItemsCnt += $oBasketItem->getAmount();
            $this->_dWeight   += $oBasketItem->getWeight();

            if ( !$oBasketItem->isDiscountArticle() && ( $oArticle = $oBasketItem->getArticle( true ) ) ) {
                $oBasketPrice = $oArticle->getBasketPrice( $oBasketItem->getAmount(), $oBasketItem->getSelList(), $this );
                $oBasketItem->setPrice( $oBasketPrice );
                //P adding product price
                $this->_oProductsPriceList->addToPriceList( $oBasketItem->getPrice() );

                $oBasketPrice->setBruttoPriceMode();
                if ( !$oArticle->skipDiscounts() && $this->canCalcDiscounts() ) {
                    // apply basket type discounts
                    //#3857 added clone in order not to influence the price
                    $aItemDiscounts = $oDiscountList->applyBasketDiscounts( clone $oBasketPrice, $oDiscountList->getBasketItemDiscounts( $oArticle, $this, $this->getBasketUser() ), $oBasketItem->getAmount() );
                    if ( is_array($this->_aItemDiscounts) && is_array($aItemDiscounts) ) {
                        $this->_aItemDiscounts = $this->_mergeDiscounts( $this->_aItemDiscounts, $aItemDiscounts);
                    }
                } else {
                    $oBasketItem->setSkipDiscounts( true );
                    $this->setSkipDiscounts( true );
                }
                $oBasketPrice->multiply( $oBasketItem->getAmount() );

                //P collect discount values for basket items which are discountable
                if ( !$oArticle->skipDiscounts() ) {
                    $this->_oDiscountProductsPriceList->addToPriceList( $oBasketPrice );
                } else {
                    $this->_oNotDiscountedProductsPriceList->addToPriceList( $oBasketPrice );
                    $oBasketItem->setSkipDiscounts( true );
                    $this->setSkipDiscounts( true );
                }
            } elseif ( $oBasketItem->isBundle() ) {
                // if bundles price is set to zero
                $oPrice = oxNew( "oxprice");
                $oBasketItem->setPrice( $oPrice );
            }
        }
    }

    /**
     * Sets discount calculation mode
     *
     * @param bool $blCalcDiscounts calculate discounts or not
     *
     * @return null
     */
    public function setDiscountCalcMode( $blCalcDiscounts )
    {
        $this->_blCalcDiscounts = $blCalcDiscounts;
    }

    /**
     * Returns true if discount calculation is enabled
     *
     * @return bool
     */
    public function canCalcDiscounts()
    {
        return $this->_blCalcDiscounts;
    }

    /**
     * Merges two discount arrays. If there are two the same
     * discounts, discount values will be added.
     *
     * @param array $aDiscounts     Discount array
     * @param array $aItemDiscounts Discount array
     *
     * @return array $aDiscounts
     */
    protected function _mergeDiscounts( $aDiscounts, $aItemDiscounts)
    {
        foreach ( $aItemDiscounts as $sKey => $oDiscount ) {
            // add prices of the same discounts
            if ( array_key_exists ($sKey, $aDiscounts) ) {
                $aDiscounts[$sKey]->dDiscount += $oDiscount->dDiscount;
            } else {
                $aDiscounts[$sKey] = $oDiscount;
            }
        }
        return $aDiscounts;
    }

    /**
     * Iterates through basket items and calculates its delivery costs
     *
     * @return oxPrice
     */
    protected function _calcDeliveryCost()
    {
        if ( $this->_oDeliveryPrice !== null ) {
            return $this->_oDeliveryPrice;
        }
        $myConfig  = $this->getConfig();
        $oDeliveryPrice = oxNew( 'oxprice' );
        $oDeliveryPrice->setBruttoPriceMode();

        // don't calculate if not logged in
        $oUser = $this->getBasketUser();

        if ( !$oUser && !$myConfig->getConfigParam( 'blCalculateDelCostIfNotLoggedIn' ) ) {
            return $oDeliveryPrice;
        }

        // VAT for delivery will be calculated always (#3757)
        // blCalcVATForDelivery option is @deprecated since 2012-03-23 in version 4.6
        // the option blShowVATForDelivery will be used only for displaying
        $fDelVATPercent = 0;
        $fDelVATPercent = $this->getMostUsedVatPercent();
        $oDeliveryPrice->setVat( $fDelVATPercent );

        // list of active delivery costs
        if ( $myConfig->getConfigParam('bl_perfLoadDelivery') ) {
            $aDeliveryList = oxDeliveryList::getInstance()->getDeliveryList( $this,
                                        $oUser,
                                        $this->_findDelivCountry(),
                                        $this->getShippingId()
                                    );

            if ( count( $aDeliveryList ) > 0 ) {
                foreach ( $aDeliveryList as $oDelivery ) {
                    //debug trace
                    if ( $myConfig->getConfigParam( 'iDebug' ) == 5 ) {
                        echo( "DelCost : ".$oDelivery->oxdelivery__oxtitle->value."<br>" );
                    }
                    $oDeliveryPrice->addPrice( $oDelivery->getDeliveryPrice( $fDelVATPercent ) );
                }
            }
        }

        return $oDeliveryPrice;
    }

    /**
     * Basket user getter
     *
     * @return oxuser
     */
    public function getBasketUser()
    {
        if ( $this->_oUser == null ) {
            return $this->getUser();
        }

        return $this->_oUser;
    }

    /**
     * Basket user setter
     *
     * @param oxuser $oUser Basket user
     *
     * @return null
     */
    public function setBasketUser( $oUser )
    {
        $this->_oUser = $oUser;
    }

    //P
    /**
     * Get most used vat percent:
     *
     * @return double
     */
    public function getMostUsedVatPercent()
    {
        return $this->_oProductsPriceList->getMostUsedVatPercent();
    }

    //P
    /**
     * Performs final sum calculations and roundings.
     *
     * @return null
     *
     */
    protected function _calcTotalPrice()
    {
        // 1. add products price
        $dprice = $this->_oProductsPriceList->getBruttoSum();
        $this->_oPrice->setPrice( $dprice );

        // 2. substract discounts
        if ( $dprice ) {

            /*
            //#3857 this section is not needed as $this->_aItemDiscounts is part of $this->_aDiscounts
            // 2.1 applying basket item discounts
            foreach ( $this->_aItemDiscounts as $oDiscount ) {

                // skipping bundle discounts
                if ( $oDiscount->sType == 'itm' ) {
                    continue;
                }
                $this->_oPrice->subtract( $oDiscount->dDiscount );
            }*/

            // 2.2 applying basket discounts
            $this->_oPrice->subtract( $this->_oTotalDiscount->getBruttoPrice() );

            // 2.3 applying voucher discounts
            if ($oVoucherDisc = $this->getVoucherDiscount()) {
                $this->_oPrice->subtract( $oVoucherDisc->getBruttoPrice() );
            }
        }

        // 2.3 add delivery cost
        if ( isset( $this->_aCosts['oxdelivery'] ) ) {
            $this->_oPrice->add( $this->_aCosts['oxdelivery']->getBruttoPrice() );
        }

        // 2.4 add wrapping price
        if ( isset( $this->_aCosts['oxwrapping'] ) ) {
            $this->_oPrice->add( $this->_aCosts['oxwrapping']->getBruttoPrice() );
        }

        // 2.5 add payment price
        if ( isset( $this->_aCosts['oxpayment'] ) ) {
            $this->_oPrice->add( $this->_aCosts['oxpayment']->getBruttoPrice() );
        }

        // 2.6 add TS protection price
        if ( isset( $this->_aCosts['oxtsprotection'] ) ) {
            $this->_oPrice->add( $this->_aCosts['oxtsprotection']->getBruttoPrice() );
        }

    }

    /**
     * Voucher discount setter
     *
     * @param double $dDiscount voucher discount value
     *
     * @return null
     */
    public function setVoucherDiscount( $dDiscount )
    {
        $this->_oVoucherDiscount = oxNew( 'oxPrice' );
        $this->_oVoucherDiscount->setBruttoPriceMode();
        $this->_oVoucherDiscount->add( $dDiscount );
    }

    /**
     * Calculates voucher discount
     *
     * @return null
     */
    protected function _calcVoucherDiscount()
    {
        if ( $this->getConfig()->getConfigParam( 'bl_showVouchers' ) && ($this->_oVoucherDiscount === null || ( $this->_blUpdateNeeded && !$this->isAdmin() ) ) ) {

            $this->_oVoucherDiscount = oxNew( 'oxPrice' );
            $this->_oVoucherDiscount->setBruttoPriceMode();


            // calculating price to apply discount
            $dPrice = $this->_oDiscountProductsPriceList->getBruttoSum() - $this->_oTotalDiscount->getBruttoPrice();

            // recalculating
            if ( count( $this->_aVouchers ) ) {
                $oLang = oxLang::getInstance();
                foreach ( $this->_aVouchers as $sVoucherId => $oStdVoucher ) {
                    $oVoucher = oxNew( 'oxvoucher' );
                    try { // checking
                        $oVoucher->load( $oStdVoucher->sVoucherId );

                        if ( !$this->_blSkipVouchersAvailabilityChecking ) {
                            $oVoucher->checkBasketVoucherAvailability( $this->_aVouchers, $dPrice );
                            $oVoucher->checkUserAvailability( $this->getBasketUser() );
                        }

                        // assigning real voucher discount value as this is the only place where real value is calculated
                        $dVoucherdiscount = $oVoucher->getDiscountValue( $dPrice );

                        // acumulating discount value
                        $this->_oVoucherDiscount->add( $dVoucherdiscount );

                        // collecting formatted for preview
                        $oStdVoucher->fVoucherdiscount = $oLang->formatCurrency( $dVoucherdiscount, $this->getBasketCurrency() );
                        $oStdVoucher->dVoucherdiscount = $dVoucherdiscount;

                        // substracting voucher discount
                        $dPrice -= $dVoucherdiscount;
                    } catch ( oxVoucherException $oEx ) {

                        // removing voucher on error
                        $oVoucher->unMarkAsReserved();
                        unset( $this->_aVouchers[$sVoucherId] );

                        // storing voucher error info
                        oxUtilsView::getInstance()->addErrorToDisplay($oEx, false, true);
                    }
                }
            }
        }
    }

    //V
    /**
     * Performs netto price and VATs calculations including discounts and vouchers.
     *
     * @return null
     *
     */
    protected function _applyDiscounts()
    {
        $dBruttoPrice = 0;
        $this->_aDiscountedVats = array();
        if ( $oPriceList = $this->getDiscountProductsPrice() ) {
            $dBruttoPrice = $oPriceList->getBruttoSum();
            $this->_aDiscountedVats = $oPriceList->getVatInfo();
        }

        //apply discounts for brutto price
        $dDiscountedBruttoPrice = $this->getDiscountedProductsBruttoPrice();
        $oTotalDiscount   = $this->getTotalDiscount();
        $oVoucherDiscount = $this->getVoucherDiscount();

        //apply discount for VATs
        if ( $dBruttoPrice &&
             ( ( $oTotalDiscount && $oTotalDiscount->getBruttoPrice() ) ||
               ( $oVoucherDiscount && $oVoucherDiscount->getBruttoPrice() )
             )
           ) {
            $dPercent = ( $dDiscountedBruttoPrice / $dBruttoPrice) * 100;
            foreach ( $this->_aDiscountedVats as $sKey => $dVat ) {
                $this->_aDiscountedVats[$sKey] = oxPrice::percent( $dVat, $dPercent);
            }
        }

        $oUtils = oxUtils::getInstance();
        $dDiscVatSum = 0;
        foreach ( $this->_aDiscountedVats as $dVat ) {
            $dDiscVatSum += $oUtils->fRound( -$dVat, $this->_oCurrency);
        }
        //calculate netto price with discounts
        $this->_dDiscountedProductNettoPrice = $dDiscountedBruttoPrice + $dDiscVatSum;
    }

    /**
     * Loads basket discounts and calculates discount values.
     *
     * @return null
     */
    protected function _calcBasketDiscount()
    {
        // resetting
        $this->_aDiscounts = array();

        // P using prices sum which has discount, not sum of skipped discounts
        $dOldprice = $this->_oDiscountProductsPriceList->getBruttoSum();

        // add basket discounts
        $aDiscounts = oxDiscountList::getInstance()->getBasketDiscounts( $this, $this->getBasketUser() );

        foreach ( $aDiscounts as $oDiscount ) {

            // storing applied discounts
            $oStdDiscount = $oDiscount->getSimpleDiscount();

            // skipping bundle discounts
            if ( $oDiscount->oxdiscount__oxaddsumtype->value == 'itm' ) {
                continue;
            }

            // saving discount info
            $oStdDiscount->dDiscount = $oDiscount->getAbsValue( $dOldprice );
            if ($dOldprice < $oStdDiscount->dDiscount) {
                $oStdDiscount->dDiscount = $dOldprice;
            }

            if ($oStdDiscount->dDiscount != 0) {
                $this->_aDiscounts[$oDiscount->getId()] = $oStdDiscount;
                // substracting product price after discount
                $dOldprice = $dOldprice - $oStdDiscount->dDiscount;
            }
        }
    }

    /**
     * Calculates total basket discount value.
     *
     * @return null
     */
    protected function _calcBasketTotalDiscount()
    {
        if ( $this->_oTotalDiscount === null || ( !$this->isAdmin() ) ) {


            $this->_oTotalDiscount = oxNew( 'oxPrice' );
            $this->_oTotalDiscount->setBruttoPriceMode();

            //#3857 merging item discounts to aDiscounts and later to oTotalDiscount
            $this->_aDiscounts = array_merge($this->_aItemDiscounts, $this->_aDiscounts);

            if ( is_array($this->_aDiscounts) ) {
                foreach ( $this->_aDiscounts as $oDiscount ) {

                    // skipping bundle discounts
                    if ( $oDiscount->sType == 'itm' ) {
                        continue;
                    }

                    // add discount value to total basket discount
                    $this->_oTotalDiscount->add( $oDiscount->dDiscount );
                }
            }
        }
    }

    /**
     * Adds Gift price info to $this->oBasket (additional field for
     * basket item "oWrap""). Loads each basket item, checks for
     * wrapping data, updates if available and stores back into
     * $this->oBasket. Returns oxprice object for wrapping.
     *
     * @return object oxPrice
     */

    protected function _calcBasketWrapping()
    {
        $myConfig = $this->getConfig();
        $oWrappingPrice = oxNew( 'oxPrice' );
        $oWrappingPrice->setBruttoPriceMode();

        // wrapping VAT will be always calculated (#3757)
        // blCalcVatForWrapping option is @deprecated since 2012-03-23 in version 4.6
        // blShowVATForWrapping option will be used only for displaying
        $dWrappingVATPercent = $this->getMostUsedVatPercent();
        $oWrappingPrice->setVat( $dWrappingVATPercent );

        // calculating basket items wrapping
        foreach ( $this->_aBasketContents as $oBasketItem ) {

            if ( ( $oWrapping = $oBasketItem->getWrapping() ) ) {
                if ($dWrappingVATPercent !== null) {
                    $oWrapping->setWrappingVat($dWrappingVATPercent);
                }
                $oWrappingPrice->addPrice( $oWrapping->getWrappingPrice( $oBasketItem->getAmount() ) );
            }
        }

        // gift card price calculation
        if ( ( $oCard = $this->getCard() ) ) {
            if ($dWrappingVATPercent !== null) {
                $oCard->setWrappingVat($dWrappingVATPercent);
            }
            $oWrappingPrice->addPrice( $oCard->getWrappingPrice() );
        }

        return $oWrappingPrice;
    }

    /**
     * Payment cost calculation, applying payment discount if available.
     * Returns oxprice object.
     *
     * @return object oxPrice
     */
    protected function _calcPaymentCost()
    {
        // resetting values
        $oPaymentPrice = oxNew( 'oxPrice' );
        $oPaymentPrice->setBruttoPriceMode();

        // payment
        if ( ( $this->_sPaymentId = $this->getPaymentId() ) ) {

            $oPayment = oxNew( 'oxpayment' );
            $oPayment->load( $this->_sPaymentId );

            $oPaymentPrice = $oPayment->getPaymentPrice( $this );
        }

        return $oPaymentPrice;
    }

    /**
     * TS Protection cost calculation.
     * Returns oxprice object.
     *
     * @return object oxPrice
     */
    protected function _calcTsProtectionCost()
    {
        // resetting values
        $oProtectionPrice = oxNew( 'oxPrice' );
        $oProtectionPrice->setBruttoPriceMode();

        // payment
        if ( ( $this->_sTsProductId = $this->getTsProductId() ) ) {

            $oTsProtection = oxNew('oxtsprotection');
            $oTsProduct = $oTsProtection->getTsProduct( $this->_sTsProductId );

            $oProtectionPrice = $oTsProduct->getPrice();
        }
        return $oProtectionPrice;
    }

    /**
     * Sets basket additional costs
     *
     * @param string $sCostName additional costs
     * @param object $oPrice    oxPrice
     *
     * @return null
     */
    public function setCost( $sCostName, $oPrice = null )
    {
        $this->_aCosts[$sCostName] = $oPrice;
    }

    /**
     * Executes all needed functions to calculate basket price and other needed
     * info
     *
     * @param bool $blForceUpdate set this parameter to TRUE to force basket recalculation
     *
     * @return null
     */
    public function calculateBasket( $blForceUpdate = false )
    {
        /*
        //would be good to perform the reset of previous calculation
        //at least you can use it for the debug
        $this->_aDiscounts = array();
        $this->_aItemDiscounts = array();
        $this->_oTotalDiscount = null;
        $this->_dDiscountedProductNettoPrice = 0;
        $this->_aDiscountedVats = array();
        $this->_oPrice = null;
        $this->_oNotDiscountedProductsPriceList = null;
        $this->_oProductsPriceList = null;
        $this->_oDiscountProductsPriceList = null;*/


        if ( !$this->isEnabled() ) {
            return;
        }

        if ( !$this->_blUpdateNeeded && !$blForceUpdate ) {
            return;
        }

        $this->_aCosts = array();

        $this->_oPrice = oxNew( 'oxprice' );
        $this->_oPrice->setBruttoPriceMode();

        //  1. saving basket to the database
        $this->_save();

        //  2. remove all bundles
        $this->_clearBundles();

        //  3. generate bundle items
        $this->_addBundles();

        // reserve active basket
        if ($this->getConfig()->getConfigParam( 'blPsBasketReservationEnabled' )) {
            $this->getSession()->getBasketReservations()->reserveBasket($this);
        }

        //  4. calculating item prices
        $this->_calcItemsPrice();

        //  5. calculating/applying discounts
        $this->_calcBasketDiscount();

        //  6. calculating basket total discount
        $this->_calcBasketTotalDiscount();

        //  7. check for vouchers
        $this->_calcVoucherDiscount();

        //  8. applies all discounts to pricelist
        $this->_applyDiscounts();

        //  9. calculating additional costs:
        //  9.1: delivery
        $this->setCost( 'oxdelivery', $this->_calcDeliveryCost() );

        //  9.2: adding wrapping costs
        $this->setCost( 'oxwrapping', $this->_calcBasketWrapping() );

        //  9.3: adding payment cost
        $this->setCost( 'oxpayment', $this->_calcPaymentCost() );

        //  9.4: adding TS protection cost
        $this->setCost( 'oxtsprotection', $this->_calcTsProtectionCost() );

        //  10. calculate total price
        $this->_calcTotalPrice();

        //  11. setting deprecated values
        $this->_setDeprecatedValues();

        //  12.setting to up-to-date status
        $this->afterUpdate();
    }

    /**
     * Notifies basket that recalculation is needed
     *
     * @return null
     */
    public function onUpdate()
    {
        $this->_blUpdateNeeded = true;
    }

    /**
     * Marks basket as up-to-date
     *
     * @return null
     */
    public function afterUpdate()
    {
        $this->_blUpdateNeeded = false;
    }

    /**
     * Function collects summary information about basket. Usually this info
     * is used while calculating discounts or so. Data is stored in static
     * class parameter oxbasket::$_aBasketSummary
     *
     * @return object
     */
    public function getBasketSummary()
    {
        if ( $this->_blUpdateNeeded || $this->_aBasketSummary === null ) {
            $this->_aBasketSummary = new Oxstdclass();
            $this->_aBasketSummary->aArticles = array();
            $this->_aBasketSummary->aCategories = array();
            $this->_aBasketSummary->iArticleCount = 0;
            $this->_aBasketSummary->dArticlePrice = 0;
            $this->_aBasketSummary->dArticleDiscountablePrice = 0;
        }

        if ( !$this->isEnabled() ) {
            return $this->_aBasketSummary;
        }

        $myConfig = $this->getConfig();
        foreach ( $this->_aBasketContents as $oBasketItem ) {
            if ( !$oBasketItem->isBundle() && $oArticle = $oBasketItem->getArticle(false) ) {
                $aCatIds = $oArticle->getCategoryIds();
                //#M530 if price is not loaded for articles
                $dPrice = 0;
                $dDiscountablePrice = 0;
                if ( ( $oPrice = $oArticle->getPrice( $oBasketItem->getAmount() ) ) ) {
                    $dPrice = $oPrice->getBruttoPrice();
                    if ( !$oArticle->skipDiscounts() ) {
                        $dDiscountablePrice = $dPrice;
                    }
                }

                foreach ( $aCatIds as $sCatId ) {
                    if ( !isset( $this->_aBasketSummary->aCategories[$sCatId] ) ) {
                        $this->_aBasketSummary->aCategories[$sCatId] = new Oxstdclass();
                    }

                    $this->_aBasketSummary->aCategories[$sCatId]->dPrice  += $dPrice * $oBasketItem->getAmount();
                    $this->_aBasketSummary->aCategories[$sCatId]->dDiscountablePrice += $dDiscountablePrice * $oBasketItem->getAmount();
                    $this->_aBasketSummary->aCategories[$sCatId]->dAmount += $oBasketItem->getAmount();
                    $this->_aBasketSummary->aCategories[$sCatId]->iCount++;
                }

                // variant handling
                if ( ($sParentId = $oArticle->getProductParentId()) && $myConfig->getConfigParam( 'blVariantParentBuyable' ) ) {
                    if ( !isset( $this->_aBasketSummary->aArticles[$sParentId] ) ) {
                        $this->_aBasketSummary->aArticles[$sParentId] = 0;
                    }
                    $this->_aBasketSummary->aArticles[$sParentId] += $oBasketItem->getAmount();
                }

                if ( !isset( $this->_aBasketSummary->aArticles[$oBasketItem->getProductId()] ) ) {
                    $this->_aBasketSummary->aArticles[$oBasketItem->getProductId()] = 0;
                }

                $this->_aBasketSummary->aArticles[$oBasketItem->getProductId()] += $oBasketItem->getAmount();
                $this->_aBasketSummary->iArticleCount += $oBasketItem->getAmount();
                $this->_aBasketSummary->dArticlePrice += $dPrice * $oBasketItem->getAmount();
                $this->_aBasketSummary->dArticleDiscountablePrice += $dDiscountablePrice * $oBasketItem->getAmount();
            }
        }
        return $this->_aBasketSummary;
    }

    /**
     * Checks and sets voucher information. Checks it's availability according
     * to few conditions: oxvoucher::checkVoucherAvailability(),
     * oxvoucher::checkUserAvailability(). Errors are stored in
     * oxbasket::voucherErrors array. After all voucher is marked as reserved
     * (oxvoucher::MarkAsReserved())
     *
     * @param string $sVoucherId voucher ID
     *
     * @return null
     */
    public function addVoucher( $sVoucherId )
    {
        // calculating price to check
        // P using prices sum which has discount, not sum of skipped discounts
        $dPrice = 0;
        if ( $this->_oDiscountProductsPriceList ) {
            $dPrice = $this->_oDiscountProductsPriceList->getBruttoSum();
        }

        try { // trying to load voucher and apply it

            $oVoucher = oxNew( 'oxvoucher' );

            if ( !$this->_blSkipVouchersAvailabilityChecking ) {
                $oVoucher->getVoucherByNr( $sVoucherId, $this->_aVouchers, true );
                $oVoucher->checkVoucherAvailability( $this->_aVouchers, $dPrice );
                $oVoucher->checkUserAvailability( $this->getBasketUser() );
                $oVoucher->markAsReserved();
            } else {
                $oVoucher->load( $sVoucherId );
            }

            // saving voucher info
            $this->_aVouchers[$oVoucher->oxvouchers__oxid->value] = $oVoucher->getSimpleVoucher();
        } catch ( oxVoucherException $oEx ) {

            // problems adding voucher
            oxUtilsView::getInstance()->addErrorToDisplay( $oEx, false, true );
        }

        $this->onUpdate();
    }

    /**
     * Removes voucher from basket and unreserves it.
     *
     * @param string $sVoucherId removable voucher ID
     *
     * @return null
     */
    public function removeVoucher( $sVoucherId )
    {
        // removing if it exists
        if ( isset( $this->_aVouchers[$sVoucherId] ) ) {

            $oVoucher = oxNew( 'oxvoucher' );
            $oVoucher->load( $sVoucherId );

            $oVoucher->unMarkAsReserved();

            // unsetting it if exists this voucher in DB or not
            unset( $this->_aVouchers[$sVoucherId] );
            $this->onUpdate();
        }

    }

    /**
     * Resets user related information kept in basket object
     *
     * @return null
     */
    public function resetUserInfo()
    {
        $this->setPayment( null );
        $this->setShipping( null );
    }

    /**
     * Sets deprecate values
     *
     * @deprecated This method as well as all deprecated class variables is deprecated
     *
     * @return null
     */
    protected function _setDeprecatedValues()
    {
        // discount information
        // formating discount value
        $this->aDiscounts = $this->getDiscounts();
        if ( count($this->aDiscounts) > 0 ) {
            $oLang = oxLang::getInstance();
            foreach ($this->aDiscounts as $oDiscount) {
                $oDiscount->fDiscount = $oLang->formatCurrency( $oDiscount->dDiscount, $this->getBasketCurrency() );
            }
        }
    }


    /**
     * Checks whether basket can be saved
     *
     * @return bool
     */
    protected function _canSaveBasket()
    {
        $blCanSave = !$this->getConfig()->getConfigParam( 'blPerfNoBasketSaving' );
        return $blCanSave;
    }

    /**
     * Populates current basket from the saved one.
     *
     * @return null
     */
    public function load()
    {
        $oUser = $this->getBasketUser();
        if ( !$oUser ) {
            return;
        }

        $oBasket = $oUser->getBasket( 'savedbasket' );

        // restoring from saved history
        $aSavedItems = $oBasket->getItems();
        foreach ( $aSavedItems as $oItem ) {
            try {
                $oSelList = $oItem->getSelList();

                $this->addToBasket( $oItem->oxuserbasketitems__oxartid->value, $oItem->oxuserbasketitems__oxamount->value, $oSelList, $oItem->getPersParams(), true );
            } catch( oxArticleException $oEx ) {
                // caught and ignored
            }
        }
    }

    /**
     * Saves existing basket to database
     *
     * @return null;
     */
    protected function _save()
    {
        if ( $this->_canSaveBasket() ) {

            if ( $oUser = $this->getBasketUser() ) {
                //first delete all contents
                //#2039
                $oSavedBasket = $oUser->getBasket( 'savedbasket' );
                $oSavedBasket->delete();

                //then save
                foreach ( $this->_aBasketContents as $oBasketItem ) {
                    // discount or bundled products will be added automatically if available
                    if ( !$oBasketItem->isBundle() && !$oBasketItem->isDiscountArticle() ) {
                       $oSavedBasket->addItemToBasket( $oBasketItem->getProductId(), $oBasketItem->getAmount(), $oBasketItem->getSelList(), true, $oBasketItem->getPersParams() );
                    }
                }
            }
        }
    }

    /**
     * Adds item to saved basket (history)
     *
     * @param string $sProductId product id
     * @param double $dAmount    item amount
     * @param array  $aSel       article select lists
     * @param bool   $blOverride override item amount or not
     * @param array  $aPersParam product persistent parameters (default null)
     *
     * @return null
     */
    /*
    protected function _addItemToSavedBasket( $sProductId , $dAmount, $aSel, $blOverride = false, $aPersParam = null )
    {
        // updating basket history
        if ( $oUser = $this->getBasketUser() ) {
            $oUser->getBasket( 'savedbasket' )->addItemToBasket( $sProductId, $dAmount, $aSel, $blOverride, $aPersParam );
        }
    }*/

    /**
     * Cleans up saved basket data. This method usually is initiated by
     * oxbasket::deleteBasket() method which cleans up basket data when
     * user completes order.
     *
     * @return null
     */
    protected function _deleteSavedBasket()
    {
        // deleting basket if session user available
        if ( $oUser = $this->getBasketUser() ) {
            $oUser->getBasket( 'savedbasket' )->delete();
        }

        // basket exclude
        if ( $this->getConfig()->getConfigParam( 'blBasketExcludeEnabled' )) {
            $this->setBasketRootCatId(null);
        }
    }

    /**
     * Tries to fetch user delivery country ID
     *
     * @return string
     */
    protected function _findDelivCountry()
    {
        $myConfig = $this->getConfig();
        $oUser    = $this->getBasketUser();

        $sDelivCountry = null;

        if ( !$oUser ) {
            // don't calculate if not logged in unless specified otherwise
            $aHomeCountry = $myConfig->getConfigParam( 'aHomeCountry' );
            if ( $myConfig->getConfigParam( 'blCalculateDelCostIfNotLoggedIn' ) && is_array( $aHomeCountry ) ) {
                $sDelivCountry = current( $aHomeCountry );
            }
        } else {

            // ok, logged in
            if ( $sCountryId = $myConfig->getGlobalParameter( 'delcountryid' ) ) {
                $sDelivCountry = $sCountryId;
            } elseif ( $sAddressId = oxSession::getVar( 'deladrid' ) ) {

                $oDelAdress = oxNew( 'oxaddress' );
                if ( $oDelAdress->load( $sAddressId ) ) {
                    $sDelivCountry = $oDelAdress->oxaddress__oxcountryid->value;
                }
            }

            // still not found ?
            if ( !$sDelivCountry ) {
                $sDelivCountry = $oUser->oxuser__oxcountryid->value;
            }
        }

        return $sDelivCountry;
    }

    /**
     * Deletes user basket object from session
     *
     * @return null
     */
    public function deleteBasket()
    {
        $this->_aBasketContents = array();
        $this->getSession()->delBasket();

        if ($this->getConfig()->getConfigParam( 'blPsBasketReservationEnabled' )) {
            $this->getSession()->getBasketReservations()->discardReservations();
        }

        // merging basket history
        $this->_deleteSavedBasket();
    }

    /**
     * Set basket payment ID
     *
     * @param string $sPaymentId payment id
     *
     * @return null
     */
    public function setPayment( $sPaymentId = null )
    {
        $this->_sPaymentId = $sPaymentId;
    }

    /**
     * Get basket payment, if payment id is not set, try to get it from session
     *
     * @return string
     */
    public function getPaymentId()
    {
        if ( !$this->_sPaymentId ) {
             $this->_sPaymentId = oxSession::getVar( 'paymentid' );
        }
        return $this->_sPaymentId;
    }

    /**
     * Set basket shipping set ID
     *
     * @param string $sShippingSetId deliveryset id
     *
     * @return null
     */
    public function setShipping( $sShippingSetId = null )
    {
        $this->_sShippingSetId = $sShippingSetId;
        oxSession::setVar( 'sShipSet', $sShippingSetId );
    }

    /**
     * Set basket shipping price
     *
     * @param string $oShippingPrice delivery costs
     *
     * @return null
     */
    public function setDeliveryPrice( $oShippingPrice = null )
    {
        $this->_oDeliveryPrice = $oShippingPrice;
    }

    /**
     * Get basket shipping set, if shipping set id is not set, try to get it from session
     *
     * @return string oxDeliverySet
     */
    public function getShippingId()
    {
        if ( !$this->_sShippingSetId ) {
             $this->_sShippingSetId = oxSession::getVar( 'sShipSet' );
        }

        $sActPaymentId = $this->getPaymentId();
        // setting default if none is set
        if ( !$this->_sShippingSetId && $sActPaymentId != 'oxempty' ) {
            $oUser = $this->getUser();

            // choosing first preferred delivery set
            list( , $sActShipSet ) = oxDeliverySetList::getInstance()->getDeliverySetData( null, $oUser, $this );
            // in case nothing was found and no user set - choosing default
            $this->_sShippingSetId = $sActShipSet ? $sActShipSet : ( $oUser ? null : 'oxidstandard' );
        } elseif ( !$this->isAdmin() && $sActPaymentId == 'oxempty' ) {
            // in case 'oxempty' is payment id - delivery set must be reset
            $this->_sShippingSetId = null;
        }

        return $this->_sShippingSetId;
    }

    /**
     * Returns array of basket oxarticle objects
     *
     * @return array
     */
    public function getBasketArticles()
    {
        $aBasketArticles = array();

        foreach ( $this->_aBasketContents as $sItemKey => $oBasketItem ) {
            try {
                $oProduct = $oBasketItem->getArticle( true );

                if ( $this->getConfig()->getConfigParam( 'bl_perfLoadSelectLists' ) ) {
                    // marking chosen select list
                    $aSelList = $oBasketItem->getSelList();
                    if ( is_array( $aSelList ) && ( $aSelectlist = $oProduct->getSelectLists( $sItemKey ) ) ) {
                        reset( $aSelList );
                        while ( list( $conkey, $iSel ) = each( $aSelList ) ) {
                            $aSelectlist[$conkey][$iSel] = $aSelectlist[$conkey][$iSel];
                            $aSelectlist[$conkey][$iSel]->selected = 1;
                        }
                        $oProduct->setSelectlist( $aSelectlist );
                    }
                }
            } catch ( oxNoArticleException $oEx ) {
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
                $this->removeItem( $sItemKey );
                $this->calculateBasket( true );
                continue;
            } catch ( oxArticleInputException $oEx ) {
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
                $this->removeItem( $sItemKey );
                $this->calculateBasket( true );
                continue;
            }

            $aBasketArticles[$sItemKey] = $oProduct;
        }
        return $aBasketArticles;
    }

    /**
     * Returns pricelist object of discounted products
     *
     * @return oxprice
     */
    public function getDiscountProductsPrice()
    {
        return $this->_oDiscountProductsPriceList;
    }

    /**
     * Returns basket products price list object
     *
     * @return oxpricelist
     */
    public function getProductsPrice()
    {
        if ( is_null($this->_oProductsPriceList) ) {
            $this->_oProductsPriceList = oxNew( 'oxPriceList' );
        }

        return $this->_oProductsPriceList;
    }

    /**
     * Returns basket price object
     *
     * @return oxprice
     */
    public function getPrice()
    {
        if ( is_null($this->_oPrice) ) {
            $this->_oPrice = oxNew( 'oxprice' );
        }

        return $this->_oPrice;
    }

    /**
     * Returns unique order ID assigned to current basket.
     * This id is only awailable on last order step
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->_sOrderId;
    }

    /**
     * Basket order ID setter
     *
     * @param string $sId unique id for basket order
     *
     * @return null
     */
    public function setOrderId( $sId )
    {
        $this->_sOrderId = $sId;
    }

    /**
     * Returns array of basket costs. By passing cost identifier method will return
     * this cost if available
     *
     * @param string $sId cost id ( optional )
     *
     * @return array
     */
    public function getCosts( $sId = null )
    {
        // if user want some specific cost - return it
        if ( $sId ) {
            return isset( $this->_aCosts[$sId] )?$this->_aCosts[$sId]:null;
        }
        return $this->_aCosts;
    }

    /**
     * Returns array of vouchers applied to basket
     *
     * @return array
     */
    public function getVouchers()
    {
        return $this->_aVouchers;
    }

    /**
     * Returns number of different products stored in basket
     *
     * @return int
     */
    public function getProductsCount()
    {
        return $this->_iProductsCnt;
    }

    /**
     * Returns count of items stored in basket
     *
     * @return double
     */
    public function getItemsCount()
    {
        return $this->_dItemsCnt;
    }

    /**
     * Returns total basket weight
     *
     * @return double
     */
    public function getWeight()
    {
        return $this->_dWeight;
    }

    /**
     * Returns basket items array
     *
     * @return array
     */
    public function getContents()
    {
        return $this->_aBasketContents;
    }

    /**
     * Returns array of palain of formatted VATs which were calculated for basket
     *
     * @param bool $blFormatCurrency enambles currency formating
     *
     * @return array
     */
    public function getProductVats( $blFormatCurrency = true)
    {
        if ( !$this->_oNotDiscountedProductsPriceList ) {
            return array();
        }

        $aVats = $this->_oNotDiscountedProductsPriceList->getVatInfo();

        $oUtils = oxUtils::getInstance();
        foreach ( $this->_aDiscountedVats as $sKey => $dVat ) {
            if ( !isset( $aVats[$sKey] ) ) {
                $aVats[$sKey] = 0;
            }
            // add prices of the same discounts
            $aVats[$sKey] += $oUtils->fRound( $dVat, $this->_oCurrency );
        }

        if ( $blFormatCurrency ) {
            $oLang = oxLang::getInstance();
            foreach ( $aVats as $sKey => $dVat ) {
                $aVats[$sKey] = $oLang->formatCurrency( $dVat, $this->getBasketCurrency() );
            }
        }

        return $aVats;
    }

    /**
     * Returns product netto price with applied discounts and vouchers
     *
     * @return double
     */
    public function getDiscountedNettoPrice()
    {
        if ( $this->_oNotDiscountedProductsPriceList ) {
            return $this->_dDiscountedProductNettoPrice + $this->_oNotDiscountedProductsPriceList->getNettoSum();
        }
        return false;
    }

    /**
     * Gift card message setter
     *
     * @param string $sMessage gift card message
     *
     * @return null
     */
    public function setCardMessage( $sMessage )
    {
        $this->_sCardMessage = $sMessage;
    }

    /**
     * Returns gift card message text
     *
     * @return string
     */
    public function getCardMessage()
    {
        return $this->_sCardMessage;
    }

    /**
     * Gift card ID setter
     *
     * @param string $sCardId gift card id
     *
     * @return null
     */
    public function setCardId( $sCardId )
    {
        $this->_sCardId = $sCardId;
    }

    /**
     * Returns applied gift card ID
     *
     * @return string
     */
    public function getCardId()
    {
        return $this->_sCardId;
    }

    /**
     * Returns gift card object (if available)
     *
     * @return oxwrapping
     */
    public function getCard()
    {
        $oCard = null;
        if ( $sCardId = $this->getCardId() ) {
            $oCard = oxNew( 'oxwrapping' );
            $oCard->load( $sCardId );
        }
        return $oCard;
    }

    /**
     * Returns total basket discount oxprice object
     *
     * @return oxprice
     */
    public function getTotalDiscount()
    {
        return $this->_oTotalDiscount;
    }

    /**
     * Returns applied discount information array
     *
     * @return array
     */
    public function getDiscounts()
    {
        if ( $this->getTotalDiscount() && $this->getTotalDiscount()->getBruttoPrice() == 0 && count($this->_aItemDiscounts) == 0) {
            return null;
        }

        //#3857 this section is not needed as $this->_aItemDiscounts is part of $this->_aDiscounts already
        //return array_merge($this->_aItemDiscounts, $this->_aDiscounts);
        return $this->_aDiscounts;
    }

    /**
     * Returns basket voucher discount oxprice object
     *
     * @return oxprice
     */
    public function getVoucherDiscount()
    {
        if ($this->getConfig()->getConfigParam( 'bl_showVouchers' )) {
            return $this->_oVoucherDiscount;
        }
        return null;
    }

    /**
     * Set basket currency
     *
     * @param object $oCurrency currency object
     *
     * @return null
     */
    public function setBasketCurrency( $oCurrency )
    {
        $this->_oCurrency = $oCurrency;
    }

    /**
     * Basket currency getter
     *
     * @return oxuser
     */
    public function getBasketCurrency()
    {
        if ( $this->_oCurrency === null ) {
            $this->_oCurrency = $this->getConfig()->getActShopCurrencyObject();
        }

        return $this->_oCurrency;
    }

    /**
     * Set skip or not vouchers availability checking
     *
     * @param bool $blSkipChecking skip or not vouchers checking
     *
     * @return null
     */
    public function setSkipVouchersChecking( $blSkipChecking = null )
    {
        $this->_blSkipVouchersAvailabilityChecking = $blSkipChecking;
    }

    /**
     * Returns true if discount must be skipped for one of the products
     *
     * @return bool
     */
    public function hasSkipedDiscount()
    {
        return $this->_blSkipDiscounts;
    }

    /**
     * Used to set "skip discounts" status for basket
     *
     * @param bool $blSkip set true to skip discounts
     *
     * @return null
     */
    public function setSkipDiscounts( $blSkip )
    {
        $this->_blSkipDiscounts = $blSkip;
    }

    /**
     * Formatted Products net price getter
     *
     * @return string
     */
    public function getProductsNetPrice()
    {
        return oxLang::getInstance()->formatCurrency( $this->getDiscountedNettoPrice(), $this->getBasketCurrency() );
    }

    /**
     * Formatted Products price getter
     *
     * @return string
     */
    public function getFProductsPrice()
    {
        if ( $this->_oProductsPriceList ) {
            return oxLang::getInstance()->formatCurrency( $this->_oProductsPriceList->getBruttoSum(), $this->getBasketCurrency() );
        }
        return null;
    }

    /**
     * Returns VAT of delivery costs
     *
     * @return double
     */
    public function getDelCostVatPercent()
    {
        return $this->getCosts( 'oxdelivery' )->getVat();
    }

    /**
     * Returns formatted VAT of delivery costs
     *
     * @return string | bool
     */
    public function getDelCostVat()
    {
        $dDelVAT = $this->getCosts( 'oxdelivery' )->getVatValue();

        // blShowVATForDelivery option will be used, only for displaying, but not calculation
        if ( $dDelVAT > 0 && $this->getConfig()->getConfigParam( 'blShowVATForDelivery' )) {
            return oxLang::getInstance()->formatCurrency( $dDelVAT, $this->getBasketCurrency() );
        }
        return false;
    }

    /**
     * Returns formatted netto price of delivery costs
     *
     * @return string
     */
    public function getDelCostNet()
    {
        $oConfig = $this->getConfig();

        // blShowVATForDelivery option will be used, only for displaying, but not calculation
        if ( $oConfig->getConfigParam( 'blShowVATForDelivery' ) && ( $this->getBasketUser() || $oConfig->getConfigParam( 'blCalculateDelCostIfNotLoggedIn' ) ) ) {
            $dNetPrice = $this->getCosts( 'oxdelivery' )->getNettoPrice();
            if ( $dNetPrice > 0 ) {
                return oxLang::getInstance()->formatCurrency( $dNetPrice, $this->getBasketCurrency() );
            }
        }
        return false;
    }

    /**
     * Returns VAT of payment costs
     *
     * @return double
     */
    public function getPayCostVatPercent()
    {
        return $this->getCosts( 'oxpayment' )->getVat();
    }

    /**
     * Returns formatted VAT of payment costs
     *
     * @return string
     */
    public function getPayCostVat()
    {
        $dPayVAT = $this->getCosts( 'oxpayment' )->getVatValue();

        // blShowVATForPayCharge option will be used, only for displaying, but not calculation
        if ( $dPayVAT > 0 && $this->getConfig()->getConfigParam( 'blShowVATForPayCharge' ) ) {
            return oxLang::getInstance()->formatCurrency( $dPayVAT, $this->getBasketCurrency() );
        }
        return false;
    }

    /**
     * Returns formatted netto price of payment costs
     *
     * @return string
     */
    public function getPayCostNet()
    {
        // blShowVATForPayCharge option will be used, only for displaying, but not calculation
        if ( $this->getConfig()->getConfigParam( 'blShowVATForPayCharge' ) ) {
            $dNetPrice = $this->getCosts( 'oxpayment' )->getNettoPrice();
            if ($dNetPrice) {
                return oxLang::getInstance()->formatCurrency( $dNetPrice, $this->getBasketCurrency() );
            }
        }
        return false;
    }

    /**
     * Returns payment costs brutto value
     *
     * @return double | bool
     */
    public function getPaymentCosts()
    {
        $oPaymentCost = $this->getCosts( 'oxpayment' );
        if ( $oPaymentCost && $oPaymentCost->getBruttoPrice() ) {
            return $oPaymentCost->getBruttoPrice();
        }
        return false;
    }

    /**
     * Returns if exists formatted payment costs
     *
     * @return string | bool
     */
    public function getFPaymentCosts()
    {
        $oPaymentCost = $this->getCosts( 'oxpayment' );

        if ( $oPaymentCost && $oPaymentCost->getBruttoPrice() ) {
            return oxLang::getInstance()->formatCurrency( $oPaymentCost->getBruttoPrice(), $this->getBasketCurrency() );
        }
        return false;
    }

    /**
     * Returns value of voucher discount
     *
     * @return double
     */
    public function getVoucherDiscValue()
    {
        if ( $this->getVoucherDiscount() ) {
            return $this->getVoucherDiscount()->getBruttoPrice();
        }
        return false;
    }

    /**
     * Returns formated voucher discount
     *
     * @return string | bool
     */
    public function getFVoucherDiscountValue()
    {
        if ( $oVoucherDiscount = $this->getVoucherDiscount() ) {
            if ( $oVoucherDiscount->getBruttoPrice() ) {
                return oxLang::getInstance()->formatCurrency( $oVoucherDiscount->getBruttoPrice(), $this->getBasketCurrency() );
            }
        }
        return false;
    }


    /**
     * Returns VAT of wrapping costs
     *
     * @return double
     */
    public function getWrappCostVatPercent()
    {
        return $this->getCosts( 'oxwrapping' )->getVat();
    }

    /**
     * Returns formatted VAT of wrapping costs
     *
     * @return string | bool
     */
    public function getWrappCostVat()
    {
        $dWrappVAT = $this->getCosts( 'oxwrapping' )->getVatValue();
        // blShowVATForWrapping option will be used, only for displaying, but not calculation
        if ( $dWrappVAT > 0 && $this->getConfig()->getConfigParam( 'blShowVATForWrapping' ) ) {
            return oxLang::getInstance()->formatCurrency( $dWrappVAT, $this->getBasketCurrency() );
        }
        return false;

    }

    /**
     * Returns formatted netto price of wrapping costs
     *
     * @return string
     */
    public function getWrappCostNet()
    {
        $dWrappNet = $this->getCosts( 'oxwrapping' )->getNettoPrice();
        // blShowVATForWrapping option will be used, only for displaying, but not calculation
        if ( $dWrappNet > 0 && $this->getConfig()->getConfigParam( 'blShowVATForWrapping' ) ) {
            return  oxLang::getInstance()->formatCurrency( $dWrappNet, $this->getBasketCurrency() );
        }
        return false;
    }

    /**
     * Returns if exists formatted wrapping costs
     *
     * @return string | bool
     */
    public function getFWrappingCosts()
    {
        $oWrappingCost = $this->getCosts( 'oxwrapping' );
        if ( $oWrappingCost && $oWrappingCost->getBruttoPrice() ) {
            return oxLang::getInstance()->formatCurrency( $oWrappingCost->getBruttoPrice(), $this->getBasketCurrency() );
        }
        return false;
    }

    /**
     * Returns formatted basket total price
     *
     * @return string
     */
    public function getFPrice()
    {
        return oxLang::getInstance()->formatCurrency( $this->getPrice()->getBruttoPrice(), $this->getBasketCurrency() );
    }

    /**
     * Returns if exists formatted delivery costs
     *
     * @return string | bool
     */
    public function getFDeliveryCosts()
    {
        $oDeliveryCost = $this->getCosts( 'oxdelivery' );
        if ( $oDeliveryCost && ( $this->getBasketUser() || $this->getConfig()->getConfigParam( 'blCalculateDelCostIfNotLoggedIn' ) ) ) {
            return oxLang::getInstance()->formatCurrency( $oDeliveryCost->getBruttoPrice(), $this->getBasketCurrency() );
        }
        return false;
    }

    /**
     * Returns if exists delivery costs
     *
     * @return string | bool
     */
    public function getDeliveryCosts()
    {
        if ( $oDeliveryCost = $this->getCosts( 'oxdelivery' ) ) {
            return $oDeliveryCost->getBruttoPrice();
        }
        return false;
    }

    /**
     * Sets total discount value
     *
     * @param double $dDiscount new total discount value
     *
     * @return null
     */
    public function setTotalDiscount( $dDiscount )
    {
        $this->_oTotalDiscount = oxNew( 'oxPrice' );
        $this->_oTotalDiscount->setBruttoPriceMode();
        $this->_oTotalDiscount->add( $dDiscount );
    }

    /**
     * Get basket price for payment cost calculation. Returned price
     * is with applied discounts, vouchers and added delivery cost
     *
     * @return double
     */
    public function getPriceForPayment()
    {
        $dPrice = $this->getDiscountedProductsBruttoPrice();
        //#1905 not discounted products should be included in payment amount calculation
        if ( $oPriceList = $this->getNotDiscountProductsPrice() ) {
            $dPrice += $oPriceList->getBruttoSum();
        }

        // adding delivery price to final price
        if ( $oDeliveryPrice = $this->_aCosts['oxdelivery'] ) {
            $dPrice += $oDeliveryPrice->getBruttoPrice();
        }

        return $dPrice;
    }

    /**
     * Returns ( current basket products sum - total discount - voucher discount )
     *
     * @return double
     */
    public function getDiscountedProductsBruttoPrice()
    {
        if ( $oProductsPrice = $this->getDiscountProductsPrice() ) {
            $dPrice = $oProductsPrice->getBruttoSum();
        }

        // substracting total discount
        if ( $oPrice = $this->getTotalDiscount() ) {
            $dPrice -= $oPrice->getBruttoPrice();
        }

        if ( $oVoucherPrice = $this->getVoucherDiscount() ) {
            $dPrice -= $oVoucherPrice->getBruttoPrice();
        }

        return $dPrice;
    }

    /**
     * Returns TRUE if ( current basket products sum - total discount - voucher discount ) > 0
     *
     * @return bool
     */
    public function isBelowMinOrderPrice()
    {
        $blIsBelowMinOrderPrice = false;
        $sConfValue = $this->getConfig()->getConfigParam( 'iMinOrderPrice' );
        if ( is_numeric($sConfValue) && $this->getProductsCount() ) {
            $dMinOrderPrice = oxPrice::getPriceInActCurrency( ( int ) $sConfValue );
            $dNotDiscountedProductPrice = 0;
            if ( $oPrice = $this->getNotDiscountProductsPrice() ) {
                $dNotDiscountedProductPrice = $oPrice->getBruttoSum();
            }
            $blIsBelowMinOrderPrice = ($dMinOrderPrice > ($this->getDiscountedProductsBruttoPrice() + $dNotDiscountedProductPrice));
        }

        return $blIsBelowMinOrderPrice;

    }

    /**
     * Returns stock of article in basket, including bundle article
     *
     * @param string $sArtId        article id
     * @param string $sExpiredArtId item id of updated article
     *
     * @return double
     */
    public function getArtStockInBasket( $sArtId, $sExpiredArtId = null )
    {
        $dArtStock = 0;
        foreach ( $this->_aBasketContents as $sItemKey => $oOrderArticle ) {
            if ( $oOrderArticle && ( $sExpiredArtId == null || $sExpiredArtId != $sItemKey ) ) {
                if ( $oOrderArticle->getArticle( true )->getId() == $sArtId ) {
                    $dArtStock += $oOrderArticle->getAmount();
                }
            }
        }

        return $dArtStock;
    }

    /**
     * Checks if product can be added to basket
     *
     * @param string $sProductId product id
     *
     * @return bool
     */
    public function canAddProductToBasket( $sProductId )
    {
        $blCanAdd = null;

        // if basket category is not set..
        if ( $this->_sBasketCategoryId === null ) {
            $oCat = null;

            // request category
            if ( $oView = $this->getConfig()->getActiveView() ) {
                if ( $oCat = $oView->getActCategory() ) {
                    if ( !$this->_isProductInRootCategory( $sProductId, $oCat->oxcategories__oxrootid->value ) ) {
                        $oCat = null;
                    } else {
                        $blCanAdd = true;
                    }
                }
            }

            // product main category
            if ( !$oCat ) {
                $oProduct = oxNew( "oxarticle" );
                if ( $oProduct->load( $sProductId ) ) {
                    $oCat = $oProduct->getCategory();
                }
            }

            // root category id
            if ( $oCat ) {
                $this->setBasketRootCatId($oCat->oxcategories__oxrootid->value);
            }
        }

        // avoiding double check..
        if ( $blCanAdd === null ) {
            $blCanAdd = $this->_sBasketCategoryId ? $this->_isProductInRootCategory( $sProductId, $this->getBasketRootCatId() ) : true;
        }

        return $blCanAdd;
    }

    /**
     * Checks if product is in root category
     *
     * @param string $sProductId product id
     * @param string $sRootCatId root category id
     *
     * @return bool
     */
    protected function _isProductInRootCategory( $sProductId, $sRootCatId )
    {
        $sO2CTable = getViewName( 'oxobject2category' );
        $sCatTable = getViewName( 'oxcategories' );

        $oDb = oxDb::getDb();
        $sParentId  = $oDb->getOne( "select oxparentid from oxarticles where oxid = ".$oDb->quote( $sProductId ) );
        $sProductId = $sParentId ? $sParentId : $sProductId;

        $sQ = "select 1 from {$sO2CTable}
                 left join {$sCatTable} on {$sCatTable}.oxid = {$sO2CTable}.oxcatnid
                 where {$sO2CTable}.oxobjectid = ".$oDb->quote( $sProductId )." and
                       {$sCatTable}.oxrootid = ".$oDb->quote( $sRootCatId );

        return (bool) $oDb->getOne( $sQ );
    }

    /**
     * Set active basket root category
     *
     * @param string $sRoot Root category id
     *
     * @return null
     */
    public function setBasketRootCatId($sRoot)
    {
        $this->_sBasketCategoryId = $sRoot;
    }

    /**
     * Get active basket root category
     *
     * @return string
     */
    public function getBasketRootCatId()
    {
        return $this->_sBasketCategoryId;
    }

    /**
     * Sets category change warn state
     *
     * @param bool $blShow to show warning or not
     *
     * @return null
     */
    public function setCatChangeWarningState( $blShow )
    {
        $this->_blShowCatChangeWarning = $blShow;
    }

    /**
     * Tells to show category change warning
     *
     * @return bool
     */
    public function showCatChangeWarning()
    {
        return $this->_blShowCatChangeWarning;
    }

    /**
     * Trusted shops protection product ID setter
     *
     * @param string $sProductId product id
     *
     * @return null
     */
    public function setTsProductId( $sProductId )
    {
        $this->_sTsProductId = $sProductId;
    }

    /**
     * Trusted shops protection product ID getter
     *
     * @return string
     */
    public function getTsProductId()
    {
        return $this->_sTsProductId;
    }

    /**
     * Returns if exists formatted TS protection costs
     *
     * @return string | bool
     */
    public function getFTsProtectionCosts()
    {
        $oProtectionCost = $this->getCosts( 'oxtsprotection' );
        if ( $oProtectionCost && $oProtectionCost->getBruttoPrice() ) {
            return oxLang::getInstance()->formatCurrency( $oProtectionCost->getBruttoPrice(), $this->getBasketCurrency() );
        }
        return false;
    }

    /**
     * Returns VAT of TS protection costs
     *
     * @return string | bool
     */
    public function getTsProtectionVatPercent()
    {
        return $this->getCosts( 'oxtsprotection' )->getVat();
    }

    /**
     * Returns formatted VAT of TS protection costs
     *
     * @return string
     */
    public function getTsProtectionVat()
    {
        $dProtectionVAT = $this->getCosts( 'oxtsprotection' )->getVatValue();
        // blShowVATForPayCharge option will be used, only for displaying, but not calculation
        if ( $dProtectionVAT > 0 && $this->getConfig()->getConfigParam( 'blShowVATForPayCharge' ) ) {
            return oxLang::getInstance()->formatCurrency( $dProtectionVAT, $this->getBasketCurrency() );
        }
        return false;
    }

    /**
     * Returns formatted netto price of TS protection costs
     *
     * @return string
     */
    public function getTsProtectionNet()
    {
        // blShowVATForPayCharge option will be used, only for displaying, but not calculation
        if ( $this->getConfig()->getConfigParam( 'blShowVATForPayCharge' ) ) {
            return oxLang::getInstance()->formatCurrency( $this->getCosts( 'oxtsprotection' )->getNettoPrice(), $this->getBasketCurrency() );
        }
        return false;
    }

    /**
     * Returns TS protection costs brutto value
     *
     * @return double
     */
    public function getTsProtectionCosts()
    {
        $oProtection = $this->getCosts( 'oxtsprotection' );
        if ( $oProtection ) {
            return $oProtection->getBruttoPrice();
        }
        return false;
    }

    /**
     * Returns pricelist object of not discounted products
     *
     * @return oxprice
     */
    public function getNotDiscountProductsPrice()
    {
        return $this->_oNotDiscountedProductsPriceList;
    }

    /**
     * Is called when new basket item is successfully added
     *
     * @param string $sProductID       id of product
     * @param double $dAmount          product amount
     * @param array  $aSel             product select lists (default null)
     * @param array  $aPersParam       product persistent parameters (default null)
     * @param bool   $blOverride       marker to acumulate passed amount or renew (default false)
     * @param bool   $blBundle         marker if product is bundle or not (default false)
     * @param string $sOldBasketItemId id if old basket item if to change it
     *
     * @return null
     */
    protected function _addedNewItem( $sProductID, $dAmount, $aSel, $aPersParam, $blOverride, $blBundle, $sOldBasketItemId )
    {
        if ( !$blOverride ) {
            $this->_blNewITemAdded = null;
            oxSession::setVar( "blAddedNewItem", true );
        }
    }

    /**
     * Resets new basket item addition state on unserialization
     *
     * @return null
     */
    public function __wakeUp()
    {
        $this->_blNewITemAdded = null;
    }

    /**
     * Returns true if new product was just added to basket
     *
     * @return bool
     */
    public function isNewItemAdded()
    {
        if ( $this->_blNewITemAdded == null ) {
            $this->_blNewITemAdded = (bool) oxSession::getVar( "blAddedNewItem" );
            oxSession::deleteVar( "blAddedNewItem" );
        }
        return $this->_blNewITemAdded;
    }

    /**
     * Returns true if at least one product is downloadable in basket
     *
     * @return bool
     */
    public function hasDownloadableProducts()
    {
        $this->_blDownloadableProducts = false;
        foreach ( $this->_aBasketContents as $sItemKey => $oOrderArticle ) {
            //#4411 getArticle() might throw an Exception
            try {
                if ( $oOrderArticle->getArticle( true )->isDownloadable() ) {
                    $this->_blDownloadableProducts = true;
                    break;
                }
            } catch(Exception $oE) {
                //#4411
                //in case product in getArticle() is not available we get an exception
                //there is nothing what we need to do here, just catch the exception and continue checking other products
            }
        }

        return $this->_blDownloadableProducts;
    }

}
