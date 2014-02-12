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
 * Admin article main delivery manager.
 * There is possibility to change delivery name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main.
 * @package admin
 */
class Delivery_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates delivery category tree,
     * passes data to Smarty engine and returns name of template file "delivery_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $oLang = oxRegistry::getLang();
        $iLang = $oLang->getTplLanguage();

        // remove itm from list
        unset( $this->_aViewData["sumtype"][2]);

        // Deliverytypes
        $aDelTypes = array();
        $oType = new stdClass();
        $oType->sType     = "a";      // amount
        $oType->sDesc    = $oLang->translateString( "amount", $iLang );
        $aDelTypes['a'] = $oType;
        $oType = new stdClass();
        $oType->sType     = "s";      // Size
        $oType->sDesc    = $oLang->translateString( "size", $iLang );
        $aDelTypes['s'] = $oType;
        $oType = new stdClass();
        $oType->sType     = "w";      // Weight
        $oType->sDesc    = $oLang->translateString( "weight", $iLang );
        $aDelTypes['w'] = $oType;
        $oType = new stdClass();
        $oType->sType     = "p";      // Price
        $oType->sDesc    = $oLang->translateString( "price", $iLang );
        $aDelTypes['p'] = $oType;

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oDelivery = oxNew( "oxdelivery" );
            $oDelivery->loadInLang( $this->_iEditLang, $soxId );

            $oOtherLang = $oDelivery->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oDelivery->loadInLang( key($oOtherLang), $soxId );
            }

            $this->_aViewData["edit"] =  $oDelivery;


            // remove already created languages
            $aLang = array_diff ( $oLang->getLanguageNames(), $oOtherLang);
            if ( count( $aLang))
                $this->_aViewData["posslang"] = $aLang;

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }

            // set selected delivery type
            if ( !$oDelivery->oxdelivery__oxdeltype->value ) {
                $oDelivery->oxdelivery__oxdeltype = new oxField("a"); // default
            }
            $aDelTypes[$oDelivery->oxdelivery__oxdeltype->value]->selected = true;
        }

        $this->_aViewData["deltypes"] = $aDelTypes;

        if ( oxConfig::getParameter("aoc") ) {
            $oDeliveryMainAjax = oxNew( 'delivery_main_ajax' );
            $this->_aViewData['oxajax'] = $oDeliveryMainAjax->getColumns();

            return "popups/delivery_main.tpl";
        }
        return "delivery_main.tpl";
    }

    /**
     * Saves delivery information changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxdelivery__oxshopid'] = $sShopID;

        $oDelivery = oxNew( "oxdelivery" );

        if ( $soxId != "-1") {
            $oDelivery->loadInLang( $this->_iEditLang, $soxId );
        } else {
            $aParams['oxdelivery__oxid'] = null;
        }

        // checkbox handling
        if ( !isset( $aParams['oxdelivery__oxactive']))
            $aParams['oxdelivery__oxactive'] = 0;
        if ( !isset( $aParams['oxdelivery__oxfixed']))
            $aParams['oxdelivery__oxfixed'] = 0;
        if ( !isset( $aParams['oxdelivery__oxfinalize']))
            $aParams['oxdelivery__oxfinalize'] = 0;
        if ( !isset( $aParams['oxdelivery__oxsort']))
            $aParams['oxdelivery__oxsort'] = 9999;


        $oDelivery->setLanguage(0);
        $oDelivery->assign( $aParams );
        $oDelivery->setLanguage($this->_iEditLang);
        $oDelivery = oxRegistry::get("oxUtilsFile")->processFiles( $oDelivery );
        $oDelivery->save();

        // set oxid if inserted
        $this->setEditObjectId( $oDelivery->getId() );
    }

    /**
     * Saves delivery information changes.
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxdelivery__oxshopid'] = $sShopID;

        $oDelivery = oxNew( "oxdelivery" );

        if ( $soxId != "-1") {
            $oDelivery->loadInLang( $this->_iEditLang, $soxId );
        } else {
            $aParams['oxdelivery__oxid'] = null;
        }

        // checkbox handling
        if ( !isset( $aParams['oxdelivery__oxactive'])) {
            $aParams['oxdelivery__oxactive'] = 0;
        }
        if ( !isset( $aParams['oxdelivery__oxfixed'])) {
            $aParams['oxdelivery__oxfixed'] = 0;
        }


        $oDelivery->setLanguage(0);
        $oDelivery->assign( $aParams);
        $oDelivery->setLanguage($this->_iEditLang);
        $oDelivery = oxRegistry::get("oxUtilsFile")->processFiles( $oDelivery );
        $oDelivery->save();

        // set oxid if inserted
        $this->setEditObjectId( $oDelivery->getId() );
    }
}
