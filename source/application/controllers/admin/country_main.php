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
 * Admin article main selectlist manager.
 * Performs collection and updatind (on user submit) main item information.
 * @package admin
 */
class Country_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxCategoryList object,
     * passes it's data to Smarty engine and returns name of template file
     * "selectlist_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();


        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oCountry = oxNew( "oxcountry" );
            $oCountry->loadInLang( $this->_iEditLang, $soxId );

            if ($oCountry->isForeignCountry()) {
                $this->_aViewData["blForeignCountry"] = true;
            } else {
                $this->_aViewData["blForeignCountry"] = false;
            }

            $oOtherLang = $oCountry->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oCountry->loadInLang( key($oOtherLang), $soxId );
            }
            $this->_aViewData["edit"] =  $oCountry;

            // remove already created languages
            $aLang = array_diff (oxRegistry::getLang()->getLanguageNames(), $oOtherLang );
            if ( count( $aLang))
                $this->_aViewData["posslang"] = $aLang;

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        } else {
            $this->_aViewData["blForeignCountry"] = true;
        }

        return "country_main.tpl";
    }

    /**
     * Saves selection list parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        $myConfig  = $this->getConfig();


        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval" );

        if ( !isset( $aParams['oxcountry__oxactive']))
            $aParams['oxcountry__oxactive'] = 0;

        $oCountry = oxNew( "oxcountry" );

        if ( $soxId != "-1") {
            $oCountry->loadInLang( $this->_iEditLang, $soxId );
        } else {
            $aParams['oxcountry__oxid']        = null;
        }

        //$aParams = $oCountry->ConvertNameArray2Idx( $aParams);
        $oCountry->setLanguage(0);
        $oCountry->assign( $aParams );
        $oCountry->setLanguage($this->_iEditLang);
        $oCountry = oxRegistry::get("oxUtilsFile")->processFiles( $oCountry );
        $oCountry->save();

        // set oxid if inserted
        $this->setEditObjectId( $oCountry->getId() );
    }

    /**
     * Saves selection list parameters changes in different language (eg. english).
     *
     * @return null
     */
    public function saveinnlang()
    {
        $myConfig  = $this->getConfig();


        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

        if ( !isset( $aParams['oxcountry__oxactive']))
            $aParams['oxcountry__oxactive'] = 0;

        $oCountry = oxNew( "oxcountry" );

        if ( $soxId != "-1")
            $oCountry->loadInLang( $this->_iEditLang, $soxId );
        else
            $aParams['oxcountry__oxid'] = null;
        //$aParams = $oCountry->ConvertNameArray2Idx( $aParams);
        $oCountry->setLanguage(0);
        $oCountry->assign( $aParams);
        $oCountry->setLanguage($this->_iEditLang);

        $oCountry->save();

        // set oxid if inserted
        $this->setEditObjectId( $oCountry->getId() );
    }
}
