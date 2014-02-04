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
 * @package model
 */
class oxDelivery extends oxI18n
{
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
     * @var oxprice
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
        $this->init( 'oxdelivery' );
        $this->setDelVatOnTop( $this->getConfig()->getConfigParam( 'blDeliveryVatOnTop' ) );
    }

    /**
     * Delivery VAT config setter
     *
     * @param bool $blOnTop delivery vat config
     *
     * @return null
     */
    public function setDelVatOnTop( $blOnTop )
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
        if ( $this->_aArtIds !== null ) {
            return $this->_aArtIds;
        }

        $oDb = oxDb::getDb();
        $sQ = "select oxobjectid from oxobject2delivery where oxdeliveryid=".$oDb->quote($this->getId())." and oxtype = 'oxarticles'";
        $aArtIds = $oDb->getAll( $sQ );

        //make single dimension array
        foreach ( $aArtIds as $aItem ) {
            $this->_aArtIds[] = $aItem[0];
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
        if ( $this->_aCatIds !== null ) {
            return $this->_aCatIds;
        }

        $oDb = oxDb::getDb();
        $sQ = "select oxobjectid from oxobject2delivery where oxdeliveryid=".$oDb->quote($this->getId())." and oxtype = 'oxcategories'";
        $aCatIds = $oDb->getAll( $sQ );

        //make single dimension array
        foreach ( $aCatIds AS $aItem ) {
            $this->_aCatIds[] = $aItem[0];
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
        return ( bool ) count( $this->getArticles() );
    }

    /**
     * Checks if delivery has assigned categories
     *
     * @return bool
     */
    public function hasCategories()
    {
        return ( bool ) count( $this->getCategories() );
    }

    /**
     * Returns amount (total net price/weight/volume/Amount) on which delivery price is applied
     *
     * @param oxBasketItem $oBasketItem basket item object
     *
     * @return double
     */
    public function getDeliveryAmount( $oBasketItem )
    {
        $dAmount  = 0;
        $oProduct = $oBasketItem->getArticle( false );

        // mark free shipping products
        if ( $oProduct->oxarticles__oxfreeshipping->value ) {
            if ($this->_blFreeShipping !== false) {
                $this->_blFreeShipping = true;
            }
        } else {

            $blExclNonMaterial = $this->getConfig()->getConfigParam( 'blExclNonMaterialFromDelivery' );
            if ( !( $oProduct->oxarticles__oxnonmaterial->value && $blExclNonMaterial ) ) {
                $this->_blFreeShipping = false;
            }

            switch ( $this->oxdelivery__oxdeltype->value ) {
                case 'p': // price
                    if ( $this->oxdelivery__oxfixed->value == 2 ) {
                        $dAmount += $oProduct->getPrice()->getPrice();
                    } else {
                        $dAmount += $oBasketItem->getPrice()->getPrice(); // price// currency conversion must allready be done in price class / $oCur->rate; // $oBasketItem->oPrice->getPrice() / $oCur->rate;
                    }
                    break;
                case 'w': // weight
                    if ( $this->oxdelivery__oxfixed->value == 2 ) {
                        $dAmount += $oProduct->oxarticles__oxweight->value;
                    } else {
                        $dAmount += $oBasketItem->getWeight();
                    }
                    break;
                case 's': // size
                    $dAmount += $oProduct->oxarticles__oxlength->value *
                                $oProduct->oxarticles__oxwidth->value *
                                $oProduct->oxarticles__oxheight->value;
                    if ( $this->oxdelivery__oxfixed->value < 2 ) {
                        $dAmount *= $oBasketItem->getAmount();
                    }
                    break;
                case 'a': // amount
                    $dAmount += $oBasketItem->getAmount();
                    break;
            }

            if ( $oBasketItem->getPrice() ) {
                $this->_dPrice   += $oBasketItem->getPrice()->getPrice();
            }
        }

        return $dAmount;
    }

    /**
     * Delivery price setter
     *
     * @param oxprice $oPrice delivery price to set
     *
     * @return null
     */
    public function setDeliveryPrice( $oPrice )
    {
        $this->_oPrice = $oPrice;
    }

    /**
     * Returns oxprice object for delivery costs
     *
     * @param double $dVat delivery vat
     *
     * @return oxPrice
     */
    public function getDeliveryPrice( $dVat = null )
    {
        if ( $this->_oPrice === null ) {
            // loading oxprice object for final price calculation
            $this->_oPrice = oxNew( 'oxPrice' );

            if ( !$this->_blDelVatOnTop ) {
                $this->_oPrice->setBruttoPriceMode();
            } else {
                $this->_oPrice->setNettoPriceMode();
            }

            $this->_oPrice->setVat( $dVat );

            // if article is free shipping, price for delivery will be not calculated
            if ( $this->_blFreeShipping ) {
                return $this->_oPrice;
            }

            // calculating base price value
            switch ( $this->oxdelivery__oxaddsumtype->value ) {
                case 'abs':

                    $dAmount = 0;

                    if ( $this->oxdelivery__oxfixed->value == 0 ) {
                        // 1. Once per Cart
                        $dAmount = 1;
                    } elseif ( $this->oxdelivery__oxfixed->value == 1 ) {
                        // 2. Once per Product overall
                        $dAmount = $this->_iProdCnt;
                    } elseif ( $this->oxdelivery__oxfixed->value == 2 ) {
                        // 3. Once per Product in Cart
                        $dAmount = $this->_iItemCnt;
                    }

                    $oCur = $this->getConfig()->getActShopCurrencyObject();
                    $this->_oPrice->add( $this->oxdelivery__oxaddsum->value * $oCur->rate );
                    $this->_oPrice->multiply( $dAmount );
                    break;
                case '%':

                    $this->_oPrice->add( $this->_dPrice /100 * $this->oxdelivery__oxaddsum->value );
                    break;
            }
        }

        // calculating total price
        return $this->_oPrice;
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOXID Object ID (default null)
     *
     * @return bool
     */
    public function delete( $sOXID = null )
    {
        if ( !$sOXID ) {
            $sOXID = $this->getId();
        }
        if ( !$sOXID ) {
            return false;
        }


        $oDb = oxDb::getDb();
        $sQ = "delete from oxobject2delivery where oxobject2delivery.oxdeliveryid = ".$oDb->quote($sOXID);
        $oDb->execute( $sQ );

        return parent::delete( $sOXID );
    }

    /**
     * Checks if delivery fits for current basket
     *
     * @param oxBasket $oBasket shop basket
     *
     * @return bool
     */
    public function isForBasket( $oBasket )
    {
        // amount for conditional check
        $blHasArticles   = $this->hasArticles();
        $blHasCategories = $this->hasCategories();
        $blUse = true;
        $iAmount = 0;
        $blForBasket = false;

        // category & article check
        if ( $blHasCategories || $blHasArticles ) {
            $blUse = false;

            $aDeliveryArticles   = $this->getArticles();
            $aDeliveryCategories = $this->getCategories();

            foreach ( $oBasket->getContents() as $oContent ) {

                //V FS#1954 - load delivery for variants from parent article
                $oArticle   = $oContent->getArticle(false);
                $sProductId = $oArticle->getProductId();
                $sParentId  = $oArticle->getProductParentId();

                if ( $blHasArticles && (in_array( $sProductId, $aDeliveryArticles ) || ( $sParentId && in_array( $sParentId, $aDeliveryArticles ) ) ) ) {
                    $blUse = true;
                    $iArtAmount = $this->getDeliveryAmount( $oContent );
                    if ( $this->oxdelivery__oxfixed->value > 0 ) {
                        if ( $this->_isForArticle( $oContent, $iArtAmount ) ) {
                            $blForBasket = true;
                        }
                    }
                    if (!$blForBasket) {
                        $iAmount += $iArtAmount;
                    }

                } elseif ( $blHasCategories ) {

                    if ( isset( self::$_aProductList[$sProductId] ) ) {
                        $oProduct = self::$_aProductList[$sProductId];
                    } else {
                        $oProduct = oxNew( 'oxArticle' );
                        $oProduct->setSkipAssign( true );

                        if ( !$oProduct->load( $sProductId ) ) {
                            continue;
                        }

                        $oProduct->setId($sProductId);
                        self::$_aProductList[$sProductId] = $oProduct;
                    }

                    foreach ( $aDeliveryCategories as $sCatId ) {

                        if ( $oProduct->inCategory( $sCatId ) ) {
                            $blUse = true;
                            $iArtAmount = $this->getDeliveryAmount( $oContent );
                            if ( $this->oxdelivery__oxfixed->value > 0 ) {
                                if ( $this->_isForArticle( $oContent, $iArtAmount ) ) {
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
            foreach ( $oBasket->getContents() as $oContent ) {
                $iArtAmount = $this->getDeliveryAmount( $oContent );
                if ( $this->oxdelivery__oxfixed->value > 0 ) {
                    if ( $this->_isForArticle( $oContent, $iArtAmount ) ) {
                        $blForBasket = true;
                    }
                }
                if (!$blForBasket) {
                    $iAmount += $iArtAmount;
                }
            }
        }

        //#M1130: Single article in Basket, checked as free shipping, is not buyable (step 3 no payments found)
        if ( !$blForBasket && $blUse && ( $this->_checkDeliveryAmount($iAmount) || $this->_blFreeShipping ) ) {
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
    protected function _isForArticle( $oContent, $iArtAmount )
    {
        if ( !$this->_blFreeShipping && $this->_checkDeliveryAmount($iArtAmount) ) {
            $this->_iItemCnt += $oContent->getAmount();
            $this->_iProdCnt += 1;
            return true;
        }
        return false;
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
        switch ( $this->oxdelivery__oxdeltype->value ) {
            case 'p': // price
                $oCur = $this->getConfig()->getActShopCurrencyObject();
                $iAmount /= $oCur->rate;
                break;
            case 'w': // weight
            case 's': // size
            case 'a': // amount
                break;
        }

        if ( $iAmount >= $this->oxdelivery__oxparam->value && $iAmount <= $this->oxdelivery__oxparamend->value ) {
            return true;
        }

        return false;
    }

    /**
     * returns delivery id
     *
     * @param string $sTitle delivery name
     *
     * @return string
     */
    public function getIdByName( $sTitle )
    {
        $oDb = oxDb::getDb();
        $sQ = "SELECT `oxid` FROM `" . getViewName( 'oxdelivery' ) . "` WHERE  `oxtitle` = " . $oDb->quote( $sTitle );
        $sId = $oDb->getOne( $sQ );

        return $sId;
    }

    /**
     * Returns array of country ISO's which are assigned to current delivery
     *
     * @return array
     */
    public function getCountriesISO()
    {
        if ( $this->_aCountriesISO === null ) {
            $oDb = oxDb::getDb();
            $this->_aCountriesISO = array();
            $sSelect = 'select oxcountry.oxisoalpha2 from oxcountry left join oxobject2delivery on oxobject2delivery.oxobjectid = oxcountry.oxid where oxobject2delivery.oxdeliveryid='.$oDb->quote( $this->getId() ).' and oxobject2delivery.oxtype = "oxcountry" ';
            $rs = $oDb->select( $sSelect );
            if ( $rs && $rs->recordCount()) {
                while ( !$rs->EOF ) {
                    $this->_aCountriesISO[] = $rs->fields[0];
                    $rs->moveNext();
                }
            }
        }
        return $this->_aCountriesISO;
    }

}
