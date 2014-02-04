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
 * Transparent shop utilities class.
 * Some specific utilities, such as fetching article info, etc. (Class may be used
 * for overriding).
 * @subpackage oxcmp
 */
class oxcmp_utils extends oxView
{
    /**
     * Marking object as component
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * If passed article ID (by URL or posted form) - loads article,
     * otherwise - loads list of action articles oxarticlelist::loadActionArticles().
     * In this case, the last list object will be used. Loaded article info
     * is serialized and outputted to client system.
     *
     * @deprecated since v5.1 (2013-09-25); not used anywhere
     *
     * @return null
     */
    public function getArticle()
    {
        $myConfig = $this->getConfig();
        $myUtils  = oxRegistry::getUtils();

        if (!$myConfig->getConfigParam("blAllowRemoteArticleInfo"))
            return false;


        $sOutput  = 'OXID__Problem : no valid oxid !';
        $oProduct = null;

        if ( ( $sId = oxConfig::getParameter( 'oxid' ) ) ) {
            $oProduct = oxNew( 'oxArticle' );
            $oProduct->load( $sId );
        } elseif ( $myConfig->getConfigParam( 'bl_perfLoadAktion' ) ) {
            $oArtList = oxNew( 'oxarticlelist');
            $oArtList->loadActionArticles( 'OXAFFILIATE' );
            $oProduct = $oArtList->current();
        }

        if ( $oProduct ) {

            $aExport = array();

            $aClassVars = get_object_vars( $oProduct );
            $oStr = getStr();

            // add all database fields
            while ( list( $sFieldName, ) = each( $aClassVars ) ) {
                if ( $oStr->strstr( $sFieldName, 'oxarticles' ) ) {
                    $sName = str_replace( 'oxarticles__', '', $sFieldName );
                    $aExport[$sName] = $oProduct->$sFieldName->value;
                }
            }

            $oPrice  = $oProduct->getPrice();

            $aExport['vatPercent'] = $oPrice->getVat();
            $aExport['netPrice']   = $myUtils->fRound( $oPrice->getNettoPrice() );
            $aExport['brutPrice']  = $myUtils->fRound( $oPrice->getBruttoPrice() );
            $aExport['vat']        = $oPrice->getVatValue();
            $aExport['fprice']     = $oProduct->getFPrice();
            $aExport['ftprice']    = $oProduct->getFTPrice();

            $aExport['oxdetaillink']     = $oProduct->getLink();
            $aExport['oxmoredetaillink'] = $oProduct->getMoreDetailLink();
            $aExport['tobasketlink']     = $oProduct->getToBasketLink();
            $aExport['thumbnaillink']    = $myConfig->getPictureUrl(null, false, $myConfig->isSsl()) ."/". $aExport['oxthumb'];
            $sOutput = serialize( $aExport );
        }

        // stop shop here
        $myUtils->showMessageAndExit( $sOutput );
    }

    /**
     * Adds/removes chosen article to/from article comparison list
     *
     * @param object $sProductId product id
     * @param double $dAmount    amount
     * @param array  $aSel       (default null)
     * @param bool   $blOverride allow override
     * @param bool   $blBundle   bundled
     *
     * @return  void
     */
    public function toCompareList( $sProductId = null, $dAmount = null, $aSel = null, $blOverride = false, $blBundle = false )
    {
        // only if enabled and not search engine..
        if ( $this->getViewConfig()->getShowCompareList() && !oxRegistry::getUtils()->isSearchEngine() ) {



            // #657 special treatment if we want to put on comparelist
            $blAddCompare  = oxConfig::getParameter( 'addcompare' );
            $blRemoveCompare = oxConfig::getParameter( 'removecompare' );
            $sProductId = $sProductId ? $sProductId:oxConfig::getParameter( 'aid' );
            if ( ( $blAddCompare || $blRemoveCompare ) && $sProductId ) {

                // toggle state in session array
                $aItems = oxSession::getVar( 'aFiltcompproducts' );
                if ( $blAddCompare && !isset( $aItems[$sProductId] ) ) {
                    $aItems[$sProductId] = true;
                }

                if ( $blRemoveCompare ) {
                    unset( $aItems[$sProductId] );
                }

                oxSession::setVar( 'aFiltcompproducts', $aItems );
                $oParentView = $this->getParent();

                // #843C there was problem then field "blIsOnComparisonList" was not set to article object
                if ( ( $oProduct = $oParentView->getViewProduct() ) ) {
                    if ( isset( $aItems[$oProduct->getId()] ) ) {
                        $oProduct->setOnComparisonList( true );
                    } else {
                        $oProduct->setOnComparisonList( false );
                    }
                }

                $aViewProds = $oParentView->getViewProductList();
                if ( is_array( $aViewProds ) && count( $aViewProds ) ) {
                    foreach ( $aViewProds as $oProduct ) {
                        if ( isset( $aItems[$oProduct->getId()] ) ) {
                            $oProduct->setOnComparisonList( true );
                        } else {
                            $oProduct->setOnComparisonList( false );
                        }
                    }
                }
            }
        }
    }

    /**
     * If session user is set loads user noticelist (oxuser::GetBasket())
     * and adds article to it.
     *
     * @param string $sProductId Product/article ID (default null)
     * @param double $dAmount    amount of good (default null)
     * @param array  $aSel       product selection list (default null)
     *
     * @return bool
     */
    public function toNoticeList( $sProductId = null, $dAmount = null, $aSel = null)
    {
        $this->_toList( 'noticelist', $sProductId, $dAmount, $aSel );
    }

    /**
     * If session user is set loads user wishlist (oxuser::GetBasket()) and
     * adds article to it.
     *
     * @param string $sProductId Product/article ID (default null)
     * @param double $dAmount    amount of good (default null)
     * @param array  $aSel       product selection list (default null)
     *
     * @return false
     */
    public function toWishList( $sProductId = null, $dAmount = null, $aSel = null )
    {
        // only if enabled
        if ( $this->getViewConfig()->getShowWishlist() ) {
            $this->_toList( 'wishlist', $sProductId, $dAmount, $aSel );
        }
    }

    /**
     * Adds chosen product to defined user list. if amount is 0, item is removed from the list
     *
     * @param string $sListType  user product list type
     * @param string $sProductId product id
     * @param double $dAmount    product amount
     * @param array  $aSel       product selection list
     *
     * @return null
     */
    protected function _toList( $sListType, $sProductId, $dAmount, $aSel )
    {
        // only if user is logged in
        if ( $oUser = $this->getUser() ) {

            $sProductId = ($sProductId) ? $sProductId : oxConfig::getParameter( 'itmid' );
            $sProductId = ($sProductId) ? $sProductId : oxConfig::getParameter( 'aid' );
            $dAmount = isset( $dAmount ) ? $dAmount : oxConfig::getParameter( 'am' );
            $aSel    = $aSel ? $aSel : oxConfig::getParameter( 'sel' );

            // processing amounts
            $dAmount = str_replace( ',', '.', $dAmount );
            if ( !$this->getConfig()->getConfigParam( 'blAllowUnevenAmounts' ) ) {
                $dAmount = round( ( string ) $dAmount );
            }

            $oBasket = $oUser->getBasket( $sListType );
            $oBasket->addItemToBasket( $sProductId, abs( $dAmount ), $aSel, ($dAmount == 0) );

            // recalculate basket count
            $oBasket->getItemCount( true );
        }
    }

    /**
     *  Set view data, call parent::render
     *
     * @return null
     */
    public function render()
    {
        parent::render();

        $myConfig = $this->getConfig();
        $oParentView = $this->getParent();

        // add content for main menu
        $oContentList = oxNew( 'oxcontentlist' );
        $oContentList->loadMainMenulist();
        $oParentView->setMenueList( $oContentList );

        return;
    }
}
