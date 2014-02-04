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
 * Admin wrapping main manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: System Administration -> Wrapping -> Main.
 * @package admin
 */
class Wrapping_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxwrapping, oxshops and oxlist
     * objects, passes data to Smarty engine and returns name of template
     * file "wrapping_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oWrapping = oxNew( "oxwrapping" );
            $oWrapping->loadInLang( $this->_iEditLang, $soxId );

            $oOtherLang = $oWrapping->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oWrapping->loadInLang( key($oOtherLang), $soxId );
            }
            $this->_aViewData["edit"] =  $oWrapping;


            // remove already created languages
            $aLang = array_diff ( oxRegistry::getLang()->getLanguageNames(), $oOtherLang );
            if ( count( $aLang))
                $this->_aViewData["posslang"] = $aLang;

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }
        }

        return "wrapping_main.tpl";
    }

    /**
     * Saves main wrapping parameters.
     *
     * @return null
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

        // checkbox handling
        if ( !isset( $aParams['oxwrapping__oxactive']))
            $aParams['oxwrapping__oxactive'] = 0;

            // shopid
            $aParams['oxwrapping__oxshopid'] = oxSession::getVar( "actshop" );

        $oWrapping = oxNew( "oxwrapping" );

        if ( $soxId != "-1") {
            $oWrapping->loadInLang( $this->_iEditLang, $soxId );
                // #1173M - not all pic are deleted, after article is removed
                oxRegistry::get("oxUtilsPic")->overwritePic( $oWrapping, 'oxwrapping', 'oxpic', 'WP', '0', $aParams, $this->getConfig()->getPictureDir(false) );
        } else
            $aParams['oxwrapping__oxid'] = null;
        //$aParams = $oWrapping->ConvertNameArray2Idx( $aParams);


        $oWrapping->setLanguage(0);
        $oWrapping->assign( $aParams);
        $oWrapping->setLanguage($this->_iEditLang);

        $oWrapping = oxRegistry::get("oxUtilsFile")->processFiles( $oWrapping );
        $oWrapping->save();

        // set oxid if inserted
        $this->setEditObjectId( $oWrapping->getId() );
    }

    /**
     * Saves main wrapping parameters.
     *
     * @return null
     */
    public function saveinnlang()
    {
        $soxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval");

        // checkbox handling
        if ( !isset( $aParams['oxwrapping__oxactive']))
            $aParams['oxwrapping__oxactive'] = 0;

            // shopid
            $aParams['oxwrapping__oxshopid'] = oxSession::getVar( "actshop" );

        $oWrapping = oxNew( "oxwrapping" );
        if ( $soxId != "-1")
            $oWrapping->load( $soxId);
        else
            $aParams['oxwrapping__oxid'] = null;
        //$aParams = $oWrapping->ConvertNameArray2Idx( $aParams);


        $oWrapping->setLanguage(0);
        $oWrapping->assign( $aParams);
        $oWrapping->setLanguage($this->_iEditLang);

        $oWrapping = oxRegistry::get("oxUtilsFile")->processFiles( $oWrapping );
        $oWrapping->save();

        // set oxid if inserted
        $this->setEditObjectId( $oWrapping->getId() );
    }
}
