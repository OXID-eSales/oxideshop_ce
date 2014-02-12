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
 * Admin article main attributes manager.
 * There is possibility to change attribute description, assign articles to
 * this attribute, etc.
 * Admin Menu: Manage Products -> Attributes -> Main.
 * @package admin
 */
class Attribute_Main extends oxAdminDetails
{
    /**
     * Loads article Attributes info, passes it to Smarty engine and
     * returns name of template file "attribute_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $myConfig = $this->getConfig();

        $oAttr = oxNew( "oxattribute" );
        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        // copy this tree for our article choose
        if ( $soxId != "-1" && isset( $soxId)) {
            // generating category tree for select list
            $this->_createCategoryTree( "artcattree", $soxId);
            // load object
            $oAttr->loadInLang( $this->_iEditLang, $soxId );


            $oOtherLang = $oAttr->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oAttr->loadInLang( key($oOtherLang), $soxId );
            }

            // remove already created languages
            $aLang = array_diff ( oxRegistry::getLang()->getLanguageNames(), $oOtherLang);
            if ( count( $aLang))
                $this->_aViewData["posslang"] = $aLang;

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] =  clone $oLang;
            }
        }

        $this->_aViewData["edit"] =  $oAttr;

        if ( $myConfig->getRequestParameter("aoc") ) {
            $oAttributeMainAjax = oxNew( 'attribute_main_ajax' );
            $this->_aViewData['oxajax'] = $oAttributeMainAjax->getColumns();

            return "popups/attribute_main.tpl";
        }
        return "attribute_main.tpl";
    }

    /**
     * Saves article attributes.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

            // shopid
            $aParams['oxattribute__oxshopid'] = oxSession::getVar( "actshop" );
        $oAttr = oxNew( "oxattribute" );

        if ( $soxId != "-1")
            $oAttr->loadInLang( $this->_iEditLang, $soxId );
        else
            $aParams['oxattribute__oxid'] = null;
        //$aParams = $oAttr->ConvertNameArray2Idx( $aParams);


        $oAttr->setLanguage(0);
        $oAttr->assign( $aParams);
        $oAttr->setLanguage($this->_iEditLang);
        $oAttr = oxRegistry::get("oxUtilsFile")->processFiles( $oAttr );
        $oAttr->save();

        $this->setEditObjectId( $oAttr->getId() );
    }

    /**
     * Saves attribute data to different language (eg. english).
     *
     * @return null
     */
    public function saveinnlang()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

            // shopid
            $aParams['oxattribute__oxshopid'] = oxSession::getVar( "actshop");
        $oAttr = oxNew( "oxattribute" );

        if ( $soxId != "-1") {
            $oAttr->loadInLang( $this->_iEditLang, $soxId );
        } else {
            $aParams['oxattribute__oxid'] = null;
        }


        $oAttr->setLanguage(0);
        $oAttr->assign( $aParams);

        // apply new language
        $oAttr->setLanguage( oxConfig::getParameter( "new_lang" ) );
        $oAttr->save();

        // set oxid if inserted
        $this->setEditObjectId( $oAttr->getId() );
    }
}
