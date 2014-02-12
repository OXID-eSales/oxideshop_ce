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
 * Admin article main deliveryset manager.
 * There is possibility to change deliveryset name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main Sets.
 * @package admin
 */
class Module_Config extends Shop_Config
{
    protected $_sModule = 'shop_config.tpl';

    /**
     * Executes parent method parent::render(), creates deliveryset category tree,
     * passes data to Smarty engine and returns name of template file "deliveryset_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig  = $this->getConfig();

        $sModuleId  = $this->_sModuleId = $this->getEditObjectId();
        $sShopId = $myConfig->getShopId();

        $oModule = oxNew( 'oxModule' );

        if ( $sModuleId && $oModule->load( $sModuleId ) ) {
            try {
                $aDbVariables = $this->_loadMetadataConfVars($oModule->getInfo("settings"));

                $this->_aViewData["var_constraints"] = $aDbVariables['constraints'];
                $this->_aViewData["var_grouping"]    = $aDbVariables['grouping'];
                $iCount = 0;
                foreach ($this->_aConfParams as $sType => $sParam) {
                    $this->_aViewData[$sParam] = $aDbVariables['vars'][$sType];
                    $iCount += count($aDbVariables['vars'][$sType]);
                }
            } catch (oxException $oEx) {
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
                $oEx->debugOut();
            }
        } else {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( new oxException('EXCEPTION_MODULE_NOT_LOADED') );
        }

        $this->_aViewData["oModule"] =  $oModule;

        return 'module_config.tpl';
    }

    /**
     * return module filter for config variables
     *
     * @return string
     */
    protected function _getModuleForConfigVars()
    {
        return oxConfig::OXMODULE_MODULE_PREFIX . $this->_sModuleId;
    }

    /**
     * Load and parse config vars from metadata.
     * Return value is a map:
     *      'vars'        => config variable values as array[type][name] = value
     *      'constraints' => constraints list as array[name] = constraint
     *      'grouping'    => grouping info as array[name] = grouping
     *
     * @param array $aModuleSettings settings array from module metadata
     *
     * @return array
     */
    public function _loadMetadataConfVars($aModuleSettings)
    {
        $oConfig  = $this->getConfig();

        $aConfVars = array(
            "bool"    => array(),
            "str"     => array(),
            "arr"     => array(),
            "aarr"    => array(),
            "select"  => array(),
        );
        $aVarConstraints = array();
        $aGrouping       = array();
        
        $aDbVariables = $this->loadConfVars($oConfig->getShopId(), $this->_getModuleForConfigVars());

        if ( is_array($aModuleSettings) ) {

            foreach ( $aModuleSettings as $aValue ) {

                $sName       = $aValue["name"];
                $sType       = $aValue["type"];
                $sValue = null;
                //$sValue      = is_null($oConfig->getConfigParam($sName))?$aValue["value"]:$oConfig->getConfigParam($sName);
                if (is_null($oConfig->getConfigParam($sName)) ) {
                    switch ($aValue["type"]){
                        case "arr":
                            $sValue = $this->_arrayToMultiline( unserialize( $aValue["value"] ) );
                            break;
                        case "aarr":
                            $sValue = $this->_aarrayToMultiline( unserialize( $aValue["value"] ) );
                            break;
                    }
                    $sValue = getStr()->htmlentities( $sValue );
                } else {
                    $sValue = $aDbVariables['vars'][$sType][$sName];
                }
                
                $sGroup      = $aValue["group"];

                $sConstraints = "";
                if ( $aValue["constraints"] ) {
                    $sConstraints = $aValue["constraints"];
                } elseif ( $aValue["constrains"] ) {
                    $sConstraints = $aValue["constrains"];
                }

                $aConfVars[$sType][$sName] = $sValue;
                $aVarConstraints[$sName]   = $this->_parseConstraint( $sType, $sConstraints );
                if ($sGroup) {
                    if (!isset($aGrouping[$sGroup])) {
                        $aGrouping[$sGroup] = array($sName=>$sType);
                    } else {
                        $aGrouping[$sGroup][$sName] = $sType;
                    }
                }
            }
        }

        return array(
            'vars'        => $aConfVars,
            'constraints' => $aVarConstraints,
            'grouping'    => $aGrouping,
        );
    }


    /**
     * Saves shop configuration variables
     *
     * @return null
     */
    public function saveConfVars()
    {
        $myConfig = $this->getConfig();


        $sModuleId  = $this->_sModuleId = $this->getEditObjectId();
        $sShopId = $myConfig->getShopId();

        $sModuleId = $this->_getModuleForConfigVars();

        foreach ($this->_aConfParams as $sType => $sParam) {
            $aConfVars = oxConfig::getParameter($sParam);
            if (is_array($aConfVars)) {
                foreach ( $aConfVars as $sName => $sValue ) {
                    $myConfig->saveShopConfVar(
                            $sType,
                            $sName,
                            $this->_serializeConfVar($sType, $sName, $sValue),
                            $sShopId,
                            $sModuleId
                    );
                }
            }
        }
    }

}
