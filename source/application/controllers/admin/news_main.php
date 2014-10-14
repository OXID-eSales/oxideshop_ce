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
 * Admin article main news manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Customer News -> News -> Main.
 * @package admin
 */
class News_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxlist object and
     * collects user groups information, passes data to Smarty engine,
     * returns name of template file "news_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

            // all usergroups
            $oGroups = oxNew( "oxlist" );
            $oGroups->init( "oxgroups" );
            $oGroups->selectString( "select * from ".getViewName( "oxgroups", $this->_iEditLang ) );

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oNews = oxNew( "oxnews" );
            $oNews->loadInLang( $this->_iEditLang, $soxId );

            $oOtherLang = $oNews->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oNews->loadInLang( key($oOtherLang), $soxId );
            }
            $this->_aViewData["edit"] =  $oNews;


            // remove already created languages
            $this->_aViewData["posslang"] =  array_diff ( oxRegistry::getLang()->getLanguageNames(), $oOtherLang);

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }
        if ( oxConfig::getParameter("aoc") ) {
            $oNewsMainAjax = oxNew( 'news_main_ajax' );
            $this->_aViewData['oxajax'] = $oNewsMainAjax->getColumns();

            return "popups/news_main.tpl";
        }
        return "news_main.tpl";
    }

    /**
     * Saves news parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");
        // checkbox handling
        if ( !isset( $aParams['oxnews__oxactive']))
            $aParams['oxnews__oxactive'] = 0;

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxnews__oxshopid'] = $sShopID;
        // creating fake object to save correct time value
        if (!$aParams['oxnews__oxdate'])
            $aParams['oxnews__oxdate'] = "";

        $oConvObject = new oxField();
        $oConvObject->fldmax_length = 0;
        $oConvObject->fldtype = "date";
        $oConvObject->value   = $aParams['oxnews__oxdate'];
        $aParams['oxnews__oxdate'] = oxRegistry::get("oxUtilsDate")->convertDBDate( $oConvObject, true);

        $oNews = oxNew( "oxnews" );

        if ( $soxId != "-1")
            $oNews->loadInLang( $this->_iEditLang, $soxId );
        else
            $aParams['oxnews__oxid'] = null;


        //$aParams = $oNews->ConvertNameArray2Idx( $aParams);

        $oNews->setLanguage(0);
        $oNews->assign( $aParams);
        $oNews->setLanguage($this->_iEditLang);
        $oNews->save();

        // set oxid if inserted
        $this->setEditObjectId( $oNews->getId() );
    }

    /**
     * Saves news parameters in different language.
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");
        // checkbox handling
        if ( !isset( $aParams['oxnews__oxactive']))
            $aParams['oxnews__oxactive'] = 0;

        parent::save();

            // shopid
            $sShopID = oxSession::getVar( "actshop");
            $aParams['oxnews__oxshopid'] = $sShopID;
        // creating fake object to save correct time value
        if (!$aParams['oxnews__oxdate'])
            $aParams['oxnews__oxdate'] = "";

        $oConvObject = new oxField();
        $oConvObject->fldmax_length = 0;
        $oConvObject->fldtype = "date";
        $oConvObject->value   = $aParams['oxnews__oxdate'];
        $aParams['oxnews__oxdate'] = oxRegistry::get("oxUtilsDate")->convertDBDate( $oConvObject, true );

        $oNews = oxNew( "oxnews" );

        if ( $soxId != "-1")
            $oNews->loadInLang( $this->_iEditLang, $soxId );
        else
            $aParams['oxnews__oxid'] = null;


        //$aParams = $oNews->ConvertNameArray2Idx( $aParams);
        $oNews->setLanguage(0);
        $oNews->assign( $aParams);

        // apply new language
        $oNews->setLanguage( oxConfig::getParameter( "new_lang" ) );
        $oNews->save();

        // set oxid if inserted
        $this->setEditObjectId( $oNews->getId() );
    }
}
