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
 * Admin vendor main screen.
 * Performs collection and updating (on user submit) main item information.
 * @package admin
 */
class Vendor_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(),
     * and returns name of template file
     * "vendor_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oVendor = oxNew( "oxvendor" );
            $oVendor->loadInLang( $this->_iEditLang, $soxId );

            $oOtherLang = $oVendor->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oVendor->loadInLang( key($oOtherLang), $soxId );
            }
            $this->_aViewData["edit"] =  $oVendor;

            // category tree
            $this->_createCategoryTree( "artcattree");

            //Disable editing for derived articles
            if ($oVendor->isDerived())
               $this->_aViewData['readonly'] = true;

            // remove already created languages
            $aLang = array_diff ( oxRegistry::getLang()->getLanguageNames(), $oOtherLang);
            if ( count( $aLang))
                $this->_aViewData["posslang"] = $aLang;

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        if ( oxConfig::getParameter( "aoc" ) ) {
            $oVendorMainAjax = oxNew( 'vendor_main_ajax' );
            $this->_aViewData['oxajax'] = $oVendorMainAjax->getColumns();

            return "popups/vendor_main.tpl";
        }
        return "vendor_main.tpl";
    }

    /**
     * Saves selection list parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval" );

        if ( !isset( $aParams['oxvendor__oxactive'] ) ) {
            $aParams['oxvendor__oxactive'] = 0;
        }

            // shopid
            $aParams['oxvendor__oxshopid'] = oxSession::getVar( "actshop");

        $oVendor = oxNew( "oxvendor" );
        if ( $soxId != "-1" )
            $oVendor->loadInLang( $this->_iEditLang, $soxId );
        else {
            $aParams['oxvendor__oxid'] = null;
        }


        $oVendor->setLanguage(0);
        $oVendor->assign( $aParams );
        $oVendor->setLanguage( $this->_iEditLang );
        $oVendor = oxRegistry::get("oxUtilsFile")->processFiles( $oVendor );
        $oVendor->save();

        // set oxid if inserted
        $this->setEditObjectId( $oVendor->getId() );
    }

    /**
     * Saves selection list parameters changes in different language (eg. english).
     *
     * @return mixed
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval" );

        if ( !isset( $aParams['oxvendor__oxactive'] ) ) {
            $aParams['oxvendor__oxactive'] = 0;
        }

            // shopid
            $aParams['oxvendor__oxshopid'] = oxSession::getVar( "actshop" );

        $oVendor = oxNew( "oxvendor" );

        if ( $soxId != "-1")
            $oVendor->loadInLang( $this->_iEditLang, $soxId );
        else {
            $aParams['oxvendor__oxid'] = null;
        }


        $oVendor->setLanguage(0);
        $oVendor->assign( $aParams );
        $oVendor->setLanguage( $this->_iEditLang );
        $oVendor = oxRegistry::get("oxUtilsFile")->processFiles( $oVendor );
        $oVendor->save();

        // set oxid if inserted
        $this->setEditObjectId( $oVendor->getId() );
    }
}
