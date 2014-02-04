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
 * Admin article main shop manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Main Menu -> Core Settings -> Main.
 * @package admin
 */
class Shop_Main extends oxAdminDetails
{
    /**
     * Shop field set size, limited to 64bit by MySQL
     *
     * @var int
     */
    const SHOP_FIELD_SET_SIZE = 64;

    /**
     * Executes parent method parent::render(), creates oxCategoryList and
     * oxshop objects, passes it's data to Smarty engine and returns name of
     * template file "shop_main.tpl".
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
            $oShop = oxNew( "oxshop" );
            $isubjlang = oxConfig::getParameter("subjlang");
            if ( !isset($isubjlang))
                $isubjlang = $this->_iEditLang;

            if ($isubjlang && $isubjlang > 0) {
                $this->_aViewData["subjlang"] =  $isubjlang;
            }

            $oShop->loadInLang( $isubjlang, $soxId );

            $this->_aViewData["edit"] =  $oShop;
            //oxSession::setVar( "actshop", $soxId);//echo "<h2>$soxId</h2>";
            oxSession::setVar( "shp", $soxId);
        }


        $this->_aViewData['IsOXDemoShop'] = $myConfig->isDemoShop();
        if ( !isset( $this->_aViewData['updatenav'] ) ) {
            $this->_aViewData['updatenav']    = oxConfig::getParameter( 'updatenav' );
        }

        return "shop_main.tpl";
    }

    /**
     * Saves changed main shop configuration parameters.
     *
     * @return null
     */
    public function save()
    {
        parent::save();

        $myConfig = $this->getConfig();
        $soxId = $this->getEditObjectId();

        $aParams  = oxConfig::getParameter( "editval");


        //  #918 S
        // checkbox handling
        $aParams['oxshops__oxactive'] = ( isset( $aParams['oxshops__oxactive'] ) && $aParams['oxshops__oxactive'] == true )? 1 : 0;
        $aParams['oxshops__oxproductive'] = ( isset( $aParams['oxshops__oxproductive']) && $aParams['oxshops__oxproductive'] == true) ? 1 : 0;

        $isubjlang = oxConfig::getParameter("subjlang");
        $iLang = ( $isubjlang && $isubjlang > 0 ) ? $isubjlang : 0;

        $oShop = oxNew( "oxshop" );
        if ( $soxId != "-1" ) {
            $oShop->loadInLang( $iLang, $soxId );
        } else {
                $aParams['oxshops__oxid'] = null;
        }

        if ( $aParams['oxshops__oxsmtp'] ) {
            $aParams['oxshops__oxsmtp'] = trim($aParams['oxshops__oxsmtp']);
        }

        $oShop->setLanguage(0);
        $oShop->assign( $aParams );
        $oShop->setLanguage($iLang );

        if ( ( $sNewSMPTPass = oxConfig::getParameter( "oxsmtppwd" ) ) ) {
            $oShop->oxshops__oxsmtppwd->setValue( $sNewSMPTPass == '-' ? "" : $sNewSMPTPass );
        }


        try {
            $oShop->save();
        } catch ( oxException $e ) {
            return;
        }

        $this->_aViewData["updatelist"] =  "1";


        oxSession::setVar( "actshop", $soxId);
    }

    /**
     * Returns array of config variables which cannot be copied
     *
     * @return array
     */
    protected function _getNonCopyConfigVars()
    {
        $aNonCopyVars = array("aSerials", "IMS", "IMD", "IMA", "blBackTag", "sUtilModule", "aModulePaths", "aModuleFiles", "aModuleEvents", "aModuleVersions", "aModuleTemplates", "aModules", "aDisabledModules");
        //adding non copable multishop field options
        $aMultiShopTables = $this->getConfig()->getConfigParam( 'aMultiShopTables' );
        foreach ( $aMultiShopTables as $sMultishopTable ) {
            $aNonCopyVars[] = 'blMallInherit_' . strtolower( $sMultishopTable );
        }

        return $aNonCopyVars;
    }

    /**
     * Copies base shop config variables to current
     *
     * @param oxshop $oShop new shop object
     *
     * @return null
     */
    protected function _copyConfigVars( $oShop )
    {
        $myConfig = $this->getConfig();
        $myUtilsObject = oxUtilsObject::getInstance();
        $oDB = oxDb::getDb();

        $aNonCopyVars = $this->_getNonCopyConfigVars();

        $sSelect = "select oxvarname, oxvartype, DECODE( oxvarvalue, ".$oDB->quote( $myConfig->getConfigParam( 'sConfigKey' ) ) .") as oxvarvalue, oxmodule from oxconfig where oxshopid = '1'";
        $rs = $oDB->execute( $sSelect );
        if ($rs != false && $rs->recordCount() > 0) {
                    while (!$rs->EOF) {
                        $sVarName = $rs->fields[0];
                        if (!in_array($sVarName, $aNonCopyVars)) {
                            $sID = $myUtilsObject->generateUID();
                            $sInsert = "insert into oxconfig (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue, oxmodule) values ( '$sID', ".$oDB->quote( $oShop->getId() )
                                            .", ".$oDB->quote( $rs->fields[0] )
                                            .", ".$oDB->quote( $rs->fields[1] )
                                            .",  ENCODE( ".$oDB->quote( $rs->fields[2] )
                                            .", '".$myConfig->getConfigParam( 'sConfigKey' )
                                            ."')"
                                            .", ".$oDB->quote( $rs->fields[3] ) . " )";
                                            $oDB->execute( $sInsert );
                        }
                        $rs->moveNext();
                    }
        }

        $sInheritAll = $oShop->oxshops__oxisinherited->value?"true":"false";
        $aMultiShopTables = $myConfig->getConfigParam( 'aMultiShopTables' );
        foreach ( $aMultiShopTables as $sMultishopTable ) {
            $myConfig->saveShopConfVar("bool", 'blMallInherit_' . strtolower($sMultishopTable), $sInheritAll, $oShop->oxshops__oxid->value);
        }
    }

}
