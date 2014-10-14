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
 * Currency manager class.
 * @subpackage oxcmp
 */
class oxcmp_cur extends oxView
{
    /**
     * Array of available currencies.
     * @var array
     */
    public $aCurrencies    = null;

    /**
     * Active currency object.
     * @var object
     */
    protected $_oActCur        = null;

    /**
     * Marking object as component
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Checks for currency parameter set in URL, session or post
     * variables. If such were found - loads all currencies possible
     * in shop, searches if passed is available (if no - default
     * currency is set the first defined in admin). Then sets currency
     * parameter so session ($myConfig->setActShopCurrency($iCur)),
     * loads basket and forces ir to recalculate (oBasket->blCalcNeeded
     * = true). Finally executes parent::init().
     *
     * @return null
     */
    public function init()
    {
        // Performance
        $myConfig = $this->getConfig();
        if ( !$myConfig->getConfigParam( 'bl_perfLoadCurrency' ) ) {
            //#861C -  show first currency
            $aCurrencies = $myConfig->getCurrencyArray();
            $this->_oActCur = current( $aCurrencies );
            return;
        }

        $iCur = oxConfig::getParameter( 'cur' );
        if ( isset( $iCur ) ) {
            $aCurrencies = $myConfig->getCurrencyArray();
            if (!isset( $aCurrencies[$iCur] ) ) {
                $iCur = 0;
            }

            // set new currency
            $myConfig->setActShopCurrency( $iCur );

            // recalc basket
            $oBasket = $this->getSession()->getBasket();
            $oBasket->onUpdate();
        }

        $iActCur = $myConfig->getShopCurrency();
        $this->aCurrencies = $myConfig->getCurrencyArray( $iActCur );

        $this->_oActCur = $this->aCurrencies[$iActCur];

        //setting basket currency (M:825)
        if ( !isset( $oBasket ) ) {
            $oBasket = $this->getSession()->getBasket();
        }
        $oBasket->setBasketCurrency( $this->_oActCur );
        parent::init();
    }

    /**
     * Executes parent::render(), passes currency object to template
     * engine and returns currencies array.
     *
     * Template variables:
     * <b>currency</b>
     *
     * @return array
     */
    public function render()
    {
        parent::render();
        $oParentView = $this->getParent();
        $oParentView->setActCurrency( $this->_oActCur );

        $oUrlUtils = oxRegistry::get("oxUtilsUrl");
        $sUrl = $oUrlUtils->cleanUrl( $this->getConfig()->getTopActiveView()->getLink(), array( "cur" ) );

        if ( $this->getConfig()->getConfigParam( 'bl_perfLoadCurrency' ) ) {
            reset( $this->aCurrencies );
            while ( list( , $oItem ) = each( $this->aCurrencies ) ) {
                $oItem->link = $oUrlUtils->processUrl( $sUrl, true, array( "cur" => $oItem->id ) );
            }
        }

        return $this->aCurrencies;
    }
}
