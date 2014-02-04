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
 * Admin article main discount manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Discounts -> Main.
 * @package admin
 */
class Discount_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates article category tree, passes
     * data to Smarty engine and returns name of template file "discount_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();
        parent::render();

        $sOxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $sOxId != "-1" && isset( $sOxId)) {
            // load object
            $oDiscount = oxNew( "oxdiscount" );
            $oDiscount->loadInLang( $this->_iEditLang, $sOxId );

            $oOtherLang = $oDiscount->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oDiscount->loadInLang( key( $oOtherLang ), $sOxId );
            }

            $this->_aViewData["edit"] =  $oDiscount;


            // remove already created languages
            $aLang = array_diff ( oxRegistry::getLang()->getLanguageNames(), $oOtherLang );

            if ( count( $aLang ) ) {
                $this->_aViewData["posslang"] = $aLang;
            }

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        if ( ( $iAoc = oxConfig::getParameter("aoc") ) ) {
            if ( $iAoc == "1" ) {
                $oDiscountMainAjax = oxNew( 'discount_main_ajax' );
                $this->_aViewData['oxajax'] = $oDiscountMainAjax->getColumns();

                return "popups/discount_main.tpl";
            } elseif ( $iAoc == "2" ) {
                // generating category tree for artikel choose select list
                $this->_createCategoryTree( "artcattree" );

                $oDiscountItemAjax = oxNew( 'discount_item_ajax' );
                $this->_aViewData['oxajax'] = $oDiscountItemAjax->getColumns();

                return "popups/discount_item.tpl";
            }
        }
        return "discount_main.tpl";
    }

    /**
     * Returns item discount product title
     *
     * @return string
     */
    public function getItemDiscountProductTitle()
    {
        $sTitle = false;
        $sOxId = $this->getEditObjectId();
        if ( $sOxId != "-1" && isset( $sOxId)) {
            $sViewName = getViewName( "oxarticles", $this->_iEditLang );
            $oDb = oxDb::getDb();
            $sQ = "select concat( $sViewName.oxartnum, ' ', $sViewName.oxtitle ) from oxdiscount
                   left join $sViewName on $sViewName.oxid=oxdiscount.oxitmartid
                   where oxdiscount.oxitmartid != '' and oxdiscount.oxid=" . $oDb->quote( $sOxId );
            $sTitle = $oDb->getOne( $sQ, false, false );
        }

        return $sTitle ? $sTitle : " -- ";
    }

    /**
     * Saves changed selected discount parameters.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $sOxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxdiscount__oxshopid'] = $sShopID;
        $oAttr = oxNew( "oxdiscount" );
        if ( $sOxId != "-1")
            $oAttr->load( $sOxId );
        else
            $aParams['oxdiscount__oxid'] = null;

        // checkbox handling
        if ( !isset( $aParams['oxdiscount__oxactive']))
            $aParams['oxdiscount__oxactive'] = 0;


        //$aParams = $oAttr->ConvertNameArray2Idx( $aParams);
        $oAttr->setLanguage(0);
        $oAttr->assign( $aParams );
        $oAttr->setLanguage($this->_iEditLang);
        $oAttr = oxRegistry::get("oxUtilsFile")->processFiles( $oAttr );
        $oAttr->save();

        // set oxid if inserted
        $this->setEditObjectId( $oAttr->getId() );
    }

    /**
     * Saves changed selected discount parameters in different language.
     *
     * @return null
     */
    public function saveinnlang()
    {
        parent::save();

        $sOxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxdiscount__oxshopid'] = $sShopID;
        $oAttr = oxNew( "oxdiscount" );
        if ( $sOxId != "-1")
            $oAttr->load( $sOxId);
        else
            $aParams['oxdiscount__oxid'] = null;
        // checkbox handling
        if ( !isset( $aParams['oxdiscount__oxactive']))
            $aParams['oxdiscount__oxactive'] = 0;


        //$aParams = $oAttr->ConvertNameArray2Idx( $aParams);
        $oAttr->setLanguage(0);
        $oAttr->assign( $aParams);
        $oAttr->setLanguage($this->_iEditLang);
        $oAttr = oxRegistry::get("oxUtilsFile")->processFiles( $oAttr );
        $oAttr->save();

        // set oxid if inserted
        $this->setEditObjectId( $oAttr->getId() );
    }
}
