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
 * Class handling theme installation, copied from oxModuleInstaller and modified
 * to fit the easier requirements of themes (mostly removed lots of methods and cache
 * handling).
 */
class oxThemeInstaller extends oxSuperCfg
{
    /**
     * Activate extension by merging theme class inheritance information with shop theme array
     *
     * @param oxTheme $oTheme
     *
     * @return bool
     */
    public function activate( oxTheme $oTheme )
    {
        $blResult = false;

        $sThemeId = $oTheme->getId();

        if ( $sThemeId ) {
            $this->_addThemeSettings( $oTheme->getInfo("settings"), $sThemeId );
            
            $blResult = true;
        }
        
        // It is crucial for the sTheme variable to be set AFTER the theme settings have been added.
        // Otherwise, _addThemeSettings() will pull values from the previously set theme.
        $sParent = $oTheme->getInfo('parentTheme');
        if ($sParent) {
            $this->getConfig()->saveShopConfVar("str", 'sTheme', $sParent);
            $this->getConfig()->saveShopConfVar("str", 'sCustomTheme', $sThemeId);
        } else {
            $this->getConfig()->saveShopConfVar("str", 'sTheme', $sThemeId);
            $this->getConfig()->saveShopConfVar("str", 'sCustomTheme', '');
        }

        return $blResult;
    }

    /**
     * Add theme settings to database.
     *
     * @param array  $aThemeSettings Theme settings array
     * @param string $sThemeId       Theme id
     *
     * @return null
     */
    protected function _addThemeSettings( $aThemeSettings, $sThemeId )
    {
        $this->_removeNotUsedSettings( $aThemeSettings, $sThemeId );
        $oConfig = $this->getConfig();
        $sShopId = $oConfig->getShopId();
        $oDb     = oxDb::getDb();

        if ( is_array($aThemeSettings) ) {
            $iPos = 1;
            
            foreach ( $aThemeSettings as $aValue ) {
                $sOxId = oxUtilsObject::getInstance()->generateUId();

                $sTheme      = 'theme:'.$sThemeId;
                $sName       = $aValue["name"];
                $sType       = $aValue["type"];
                
                // Use getShopConfVar() instead of getConfigParam() to ensure we are fetching from the correct them.
                // If two themes have a variable with the same name, getConfigParam() might return the wrong one.
                $sValue      = is_null( $oConfig->getShopConfVar($sName, $sShopId, $sTheme) ) ? $aValue["value"] : $oConfig->getShopConfVar($sName, $sShopId, $sTheme);
                $sGroup      = $aValue["group"];

                $sConstraints = "";
                if ( $aValue["constraints"] ) {
                    $sConstraints = $aValue["constraints"];
                } elseif ( $aValue["constrains"] ) {
                    $sConstraints = $aValue["constrains"];
                }

                $iPosition   = $iPos++;

                $oConfig->setConfigParam($sName, $sValue);
                $oConfig->saveShopConfVar($sType, $sName, $sValue, $sShopId, $sTheme);

                $sDeleteSql = "DELETE FROM `oxconfigdisplay` WHERE OXCFGMODULE=".$oDb->quote($sTheme)." AND OXCFGVARNAME=".$oDb->quote($sName);
                $sInsertSql = "INSERT INTO `oxconfigdisplay` (`OXID`, `OXCFGMODULE`, `OXCFGVARNAME`, `OXGROUPING`, `OXVARCONSTRAINT`, `OXPOS`) ".
                    "VALUES ('{$sOxId}', ".$oDb->quote($sTheme).", ".$oDb->quote($sName).", ".$oDb->quote($sGroup).", ".$oDb->quote($sConstraints).", ".$oDb->quote($iPosition).")";

                $oDb->execute( $sDeleteSql );
                $oDb->execute( $sInsertSql );
            }
        }
    }

    /**
     * Removes configs which are removed from theme metadata
     *
     * @param $aThemeSettings
     * @param $sThemeId
     */
    protected function _removeNotUsedSettings( $aThemeSettings, $sThemeId )
    {
        $aModuleConfigs = $this->_getModuleConfigs( $sThemeId );
        $aThemeSettings = $this->_parseThemeSettings( $aThemeSettings );

        $aConfigsToRemove = array_diff( $aModuleConfigs, $aThemeSettings );
        if ( !empty( $aConfigsToRemove ) ) {
            $this->_removeThemeConfigs( $sThemeId, $aConfigsToRemove );
        }
    }

    /**
     * Returns theme configuration from database
     *
     * @param $sThemeId
     * @return array
     */
    protected function _getModuleConfigs( $sThemeId )
    {
        $oDb = oxDb::getDb();
        $sQuotedShopId = $oDb->quote( $this->getConfig()->getShopId() );
        $sQuotedThemeId = $oDb->quote( 'theme:' . $sThemeId );

        $sThemeConfigsQuery = "SELECT oxvarname FROM oxconfig WHERE oxmodule = $sQuotedThemeId AND oxshopid = $sQuotedShopId";

        return $oDb->getCol( $sThemeConfigsQuery );
    }

    /**
     * Parses theme config variable names to array from theme settings
     *
     * @param $aThemeSettings
     * @return array
     */
    protected function _parseThemeSettings( $aThemeSettings )
    {
        $aSettings = array();

        if ( is_array( $aThemeSettings ) ) {
            foreach( $aThemeSettings as $aSetting ) {
                $aSettings[] = $aSetting['name'];
            }
        }

        return $aSettings;
    }

    /**
     * Removes theme configs from database
     *
     * @param $sThemeId
     * @param $aConfigsToRemove
     */
    protected function _removeThemeConfigs( $sThemeId, $aConfigsToRemove )
    {
        $oDb = oxDb::getDb();
        $sQuotedShopId = $oDb->quote( $this->getConfig()->getShopId() );
        $sQuotedThemeId = $oDb->quote( 'module:' . $sThemeId );

        $aQuotedConfigsToRemove = array_map( array( $oDb, 'quote' ), $aConfigsToRemove );
        $sDeleteSql = "DELETE
                       FROM `oxconfig`
                       WHERE oxmodule = $sQuotedThemeId AND
                             oxshopid = $sQuotedShopId AND
                             oxvarname IN (".implode(", ", $aQuotedConfigsToRemove ).")";

        $oDb->execute( $sDeleteSql );
    }
}