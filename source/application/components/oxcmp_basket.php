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
 * Main shopping basket manager. Arranges shopping basket
 * contents, updates amounts, prices, taxes etc.
 * @subpackage oxcmp
 */
class oxcmp_basket extends oxView
{

    /**
     * Marking object as component
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Last call function name
     * @var string
     */
    protected $_sLastCallFnc = null;

    /**
     * Parameters which are kept when redirecting after user
     * puts something to basket
     * @var array
     */
    public $aRedirectParams = array( 'cnid',        // category id
                                     'mnid',        // manufacturer id
                                     'anid',        // active article id
                                     'tpl',         // spec. template
                                     'listtype',    // list type
                                     'searchcnid',  // search category
                                     'searchvendor',// search vendor
                                     'searchmanufacturer',// search manufacturer
                                     'searchtag',   // search tag
                                     'searchrecomm',// search recomendation
                                     'recommid'     // recomm. list id
                                    );

    /**
     * Initiates component.
     *
     * @return null
     */
    public function init()
    {
        $oConfig = $this->getConfig();
        if ($oConfig->getConfigParam( 'blPsBasketReservationEnabled' )) {
            if ($oReservations = $this->getSession()->getBasketReservations()) {
                if (!$oReservations->getTimeLeft()) {
                    $oBasket = $this->getSession()->getBasket();
                    if ( $oBasket && $oBasket->getProductsCount() ) {
                        $oBasket->deleteBasket();
                    }
                }
                $iLimit = (int) $oConfig->getConfigParam( 'iBasketReservationCleanPerRequest' );
                if (!$iLimit) {
                    $iLimit = 200;
                }
                $oReservations->discardUnusedReservations($iLimit);
            }
        }

        parent::init();

        // Basket exclude
        if ( $this->getConfig()->getConfigParam( 'blBasketExcludeEnabled' ) ) {
            if ( $oBasket = $this->getSession()->getBasket() ) {
                $this->getParent()->setRootCatChanged( $this->isRootCatChanged() && $oBasket->getContents() );
            }
        }
    }

    /**
     * Loads basket ($oBasket = $mySession->getBasket()), calls oBasket->calculateBasket,
     * executes parent::render() and returns basket object.
     *
     * @return object   $oBasket    basket object
     */
    public function render()
    {
        // recalculating
        if ( $oBasket = $this->getSession()->getBasket() ) {
            $oBasket->calculateBasket( false );
        }

        parent::render();

        return $oBasket;
    }

    /**
     * Basket content update controller.
     * Before adding article - check if client is not a search engine. If
     * yes - exits method by returning false. If no - executes
     * oxcmp_basket::_addItems() and puts article to basket.
     * Returns position where to redirect user browser.
     *
     * @param string $sProductId Product ID (default null)
     * @param double $dAmount    Product amount (default null)
     * @param array  $aSel       (default null)
     * @param array  $aPersParam (default null)
     * @param bool   $blOverride If true means increase amount of chosen article (default false)
     *
     * @return mixed
     */
    public function tobasket( $sProductId = null, $dAmount = null, $aSel = null, $aPersParam = null, $blOverride = false )
    {
        // adding to basket is not allowed ?
        $myConfig = $this->getConfig();
        if ( oxRegistry::getUtils()->isSearchEngine() ) {
            return;
        }

        // adding articles
        if ( $aProducts = $this->_getItems( $sProductId, $dAmount, $aSel, $aPersParam, $blOverride ) ) {

            $this->_setLastCallFnc( 'tobasket' );
            $oBasketItem = $this->_addItems( $aProducts );

            // new basket item marker
            if ( $oBasketItem && $myConfig->getConfigParam( 'iNewBasketItemMessage' ) != 0 ) {
                $oNewItem = new stdClass();
                $oNewItem->sTitle  = $oBasketItem->getTitle();
                $oNewItem->sId     = $oBasketItem->getProductId();
                $oNewItem->dAmount = $oBasketItem->getAmount();
                $oNewItem->dBundledAmount = $oBasketItem->getdBundledAmount();

                // passing article
                oxSession::setVar( '_newitem', $oNewItem );
            }


            // redirect to basket
            return $this->_getRedirectUrl();
        }
    }

    /**
     * Similar to tobasket, except that as product id "bindex" parameter is (can be) taken
     *
     * @param string $sProductId Product ID (default null)
     * @param double $dAmount    Product amount (default null)
     * @param array  $aSel       (default null)
     * @param array  $aPersParam (default null)
     * @param bool   $blOverride If true means increase amount of chosen article (default false)
     *
     * @return mixed
     */
    public function changebasket( $sProductId = null, $dAmount = null, $aSel = null, $aPersParam = null, $blOverride = true )
    {
        // adding to basket is not allowed ?
        if ( oxRegistry::getUtils()->isSearchEngine() ) {
            return;
        }

        // fetching item ID
        if (!$sProductId) {
            $sBasketItemId = oxConfig::getParameter( 'bindex' );

            if ( $sBasketItemId ) {
                $oBasket = $this->getSession()->getBasket();
                //take params
                $aBasketContents = $oBasket->getContents();
                $sProductId = isset( $aBasketContents[$sBasketItemId] )?$aBasketContents[$sBasketItemId]->getProductId():null;
            } else {
                $sProductId = oxConfig::getParameter( 'aid' );
            }
        }

        // fetching other needed info
        $dAmount = isset( $dAmount )?$dAmount:oxConfig::getParameter( 'am' );
        $aSel = isset( $aSel )?$aSel:oxConfig::getParameter( 'sel' );
        $aPersParam = $aPersParam?$aPersParam:oxConfig::getParameter( 'persparam' );

        // adding articles
        if ( $aProducts = $this->_getItems( $sProductId, $dAmount, $aSel, $aPersParam, $blOverride ) ) {

            // information that last call was changebasket
            $oBasket = $this->getSession()->getBasket();
            $oBasket->onUpdate();

            $this->_setLastCallFnc( 'changebasket' );
            $oBasketItem = $this->_addItems( $aProducts );
        }

    }

    /**
     * Formats and returns redirect URL where shop must be redirected after
     * storing something to basket
     *
     * @return string   $sClass.$sPosition  redirection URL
     */
    protected function _getRedirectUrl()
    {

        // active class
        $sClass = oxConfig::getParameter( 'cl' );
        $sClass = $sClass?$sClass.'?':'start?';
        $sPosition = '';

        // setting redirect parameters
        foreach ( $this->aRedirectParams as $sParamName ) {
            $sParamVal  = oxConfig::getParameter( $sParamName );
            $sPosition .= $sParamVal?$sParamName.'='.$sParamVal.'&':'';
        }

        // special treatment
        // search param
        $sParam     = rawurlencode( oxConfig::getParameter( 'searchparam', true ) );
        $sPosition .= $sParam?'searchparam='.$sParam.'&':'';

        // current page number
        $iPageNr    = (int) oxConfig::getParameter( 'pgNr' );
        $sPosition .= ( $iPageNr > 0 )?'pgNr='.$iPageNr.'&':'';

        // reload and backbutton blocker
        if ( $this->getConfig()->getConfigParam( 'iNewBasketItemMessage' ) == 3 ) {

            // saving return to shop link to session
            oxSession::setVar( '_backtoshop', $sClass.$sPosition );

            // redirecting to basket
            $sClass = 'basket?';
        }

        return $sClass.$sPosition;
    }

    /**
     * Collects and returns array of items to add to basket. Product info is taken not only from
     * given parameters, but additionally from request 'aproducts' parameter
     *
     * @param string $sProductId product ID
     * @param double $dAmount    product amount
     * @param array  $aSel       product select lists
     * @param array  $aPersParam product persistent parameters
     * @param bool   $blOverride amount override status
     *
     * @return mixed
     */
    protected function _getItems( $sProductId = null, $dAmount = null, $aSel = null, $aPersParam = null, $blOverride = false )
    {
        // collecting items to add
        $aProducts = oxConfig::getParameter( 'aproducts' );

        // collecting specified item
        $sProductId = $sProductId?$sProductId:oxConfig::getParameter( 'aid' );
        if ( $sProductId ) {

            // additionally fething current product info
            $dAmount = isset( $dAmount ) ? $dAmount : oxConfig::getParameter( 'am' );

            // select lists
            $aSel = isset( $aSel )?$aSel:oxConfig::getParameter( 'sel' );

            // persistent parameters
            if ( empty($aPersParam) ) {
                $aPersParam = oxConfig::getParameter( 'persparam' );
                if ( !is_array($aPersParam) || empty($aPersParam['details']) ) {
                    $aPersParam = null;
                }
            }

            $sBasketItemId = oxConfig::getParameter( 'bindex' );

            $aProducts[$sProductId] = array( 'am' => $dAmount,
                                             'sel' => $aSel,
                                             'persparam' => $aPersParam,
                                             'override'  => $blOverride,
                                             'basketitemid' => $sBasketItemId
                                           );
        }

        if ( is_array( $aProducts ) && count( $aProducts ) ) {

            if (oxConfig::getParameter( 'removeBtn' ) !== null) {
                //setting amount to 0 if removing article from basket
                foreach ( $aProducts as $sProductId => $aProduct ) {
                    if ( isset($aProduct['remove']) && $aProduct['remove']) {
                        $aProducts[$sProductId]['am'] = 0;
                    } else {
                        unset ($aProducts[$sProductId]);
                    }
                }
            }

            return $aProducts;
        }

        return false;
    }

    /**
     * Adds all articles user wants to add to basket. Returns
     * last added to basket item.
     *
     * @param array $aProducts products to add array
     *
     * @return  object  $oBasketItem    last added basket item
     */
    protected function _addItems ( $aProducts )
    {
        $oActView   = $this->getConfig()->getActiveView();
        $sErrorDest = $oActView->getErrorDestination();

        $oBasket = $this->getSession()->getBasket();
        $oBasketInfo = $oBasket->getBasketSummary();

        foreach ( $aProducts as $sAddProductId => $aProductInfo ) {

            $sProductId = isset( $aProductInfo['aid'] ) ? $aProductInfo['aid'] : $sAddProductId;

            // collecting input
            $aProducts[$sAddProductId]['oldam'] = isset( $oBasketInfo->aArticles[$sProductId] ) ? $oBasketInfo->aArticles[$sProductId] : 0;

            $dAmount = isset( $aProductInfo['am'] )?$aProductInfo['am']:0;
            $aSelList = isset( $aProductInfo['sel'] )?$aProductInfo['sel']:null;
            $aPersParam = ( isset( $aProductInfo['persparam'] ) && is_array( $aProductInfo['persparam'] ) && strlen( $aProductInfo['persparam']['details'] ) )?$aProductInfo['persparam']:null;
            $blOverride = isset( $aProductInfo['override'] )?$aProductInfo['override']:null;
            $blIsBundle = isset( $aProductInfo['bundle'] )?true:false;
            $sOldBasketItemId = isset( $aProductInfo['basketitemid'] )?$aProductInfo['basketitemid']:null;

            try {
                $oBasketItem = $oBasket->addToBasket( $sProductId, $dAmount, $aSelList, $aPersParam, $blOverride, $blIsBundle, $sOldBasketItemId );
            } catch ( oxOutOfStockException $oEx ) {
                $oEx->setDestination( $sErrorDest );
                // #950 Change error destination to basket popup
                if ( !$sErrorDest  && $this->getConfig()->getConfigParam( 'iNewBasketItemMessage') == 2) {
                    $sErrorDest = 'popup';
                }
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false, (bool) $sErrorDest, $sErrorDest );
            } catch ( oxArticleInputException $oEx ) {
                //add to display at specific position
                $oEx->setDestination( $sErrorDest );
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false, (bool) $sErrorDest, $sErrorDest );
            } catch ( oxNoArticleException $oEx ) {
                //ignored, best solution F ?
            }
            if ( !$oBasketItem ) {
                $oInfo = $oBasket->getBasketSummary();
                $aProducts[$sAddProductId]['am'] = isset( $oInfo->aArticles[$sProductId] ) ? $oInfo->aArticles[$sProductId] : 0;
            }
        }

        //if basket empty remove posible gift card
        if ( $oBasket->getProductsCount() == 0 ) {
            $oBasket->setCardId( null );
        }

        // information that last call was tobasket
        $this->_setLastCall( $this->_getLastCallFnc(), $aProducts, $oBasketInfo );

        return $oBasketItem;
    }

    /**
     * Setting last call data to session (data used by econda)
     *
     * @param string $sCallName    name of action ('tobasket', 'changebasket')
     * @param array  $aProductInfo data which comes from request when you press button "to basket"
     * @param array  $aBasketInfo  array returned by oxbasket::getBasketSummary()
     *
     * @return null
     */
    protected function _setLastCall( $sCallName, $aProductInfo, $aBasketInfo )
    {
        oxSession::setVar( 'aLastcall', array( $sCallName => $aProductInfo ) );
    }

    /**
     * Setting last call function name (data used by econda)
     *
     * @param string $sCallName name of action ('tobasket', 'changebasket')
     *
     * @return null
     */
    protected function _setLastCallFnc( $sCallName )
    {
        $this->_sLastCallFnc = $sCallName;
    }

    /**
     * Getting last call function name (data used by econda)
     *
     * @return string
     */
    protected function _getLastCallFnc()
    {
        return $this->_sLastCallFnc;
    }

    /**
     * Returns true if active root category was changed
     *
     * @return bool
     */
    public function isRootCatChanged()
    {
        // in Basket
        $oBasket = $this->getSession()->getBasket();
        if ( $oBasket->showCatChangeWarning() ) {
            $oBasket->setCatChangeWarningState( false );
            return true;
        }

        // in Category, only then category is empty ant not equal to default category
        $sDefCat = oxRegistry::getConfig()->getActiveShop()->oxshops__oxdefcat->value;
        $sActCat = oxConfig::getParameter( 'cnid' );
        $oActCat = oxnew('oxcategory');
        if ($sActCat && $sActCat!=$sDefCat && $oActCat->load($sActCat) ) {
            $sActRoot = $oActCat->oxcategories__oxrootid->value;
            if ( $oBasket->getBasketRootCatId() && $sActRoot != $oBasket->getBasketRootCatId() ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Executes user choice:
     *
     * - if user clicked on "Proceed to checkout" - redirects to basket,
     * - if clicked "Continue shopping" - clear basket
     *
     * @return mixed
     */
    public function executeuserchoice()
    {

        // redirect to basket
        if ( oxConfig::getParameter( "tobasket" ) ) {
            return "basket";
        } else {
            // clear basket
            $this->getSession()->getBasket()->deleteBasket();
            $this->getParent()->setRootCatChanged( false );
        }
    }

}
