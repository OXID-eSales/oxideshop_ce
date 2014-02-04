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
 * Admin shop config manager.
 * Collects shop config information, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> General.
 * @package admin
 */
class Shop_Config extends oxAdminDetails
{
    protected $_sThisTemplate = 'shop_config.tpl';
    protected $_aSkipMultiline = array('aHomeCountry', 'iShopID_TrustedShops', 'aTsUser', 'aTsPassword');
    protected $_aParseFloat = array('iMinOrderPrice');

    protected $_aConfParams = array(
        "bool"   => 'confbools',
        "str"    => 'confstrs',
        "arr"    => 'confarrs',
        "aarr"   => 'confaarrs',
        "select" => 'confselects',
        "num"    => 'confnum',
    );

    /**
     * Executes parent method parent::render(), passes shop configuration parameters
     * to Smarty and returns name of template file "shop_config.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig  = $this->getConfig();

        parent::render();


        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $this->_aViewData["edit"] = $oShop = $this->_getEditShop( $soxId );

            try {
                // category choosen as default
                $this->_aViewData["defcat"] = null;
                if ($oShop->oxshops__oxdefcat->value) {
                    $oCat = oxNew( "oxCategory" );
                    if ($oCat->load($oShop->oxshops__oxdefcat->value)) {
                        $this->_aViewData["defcat"] = $oCat;
                    }
                }
            } catch ( Exception $oExcp ) {
                // on most cases this means that views are broken, so just
                // outputting notice and keeping functionality flow ..
                $this->_aViewData["updateViews"] = 1;
            }

            $iAoc = oxConfig::getParameter("aoc");
            if ( $iAoc == 1 ) {
                $oShopDefaultCategoryAjax = oxNew( 'shop_default_category_ajax' );
                $this->_aViewData['oxajax'] = $oShopDefaultCategoryAjax->getColumns();

                return "popups/shop_default_category.tpl";
            }

        }

        $aDbVariables = $this->loadConfVars($soxId, $this->_getModuleForConfigVars());
        $aConfVars = $aDbVariables['vars'];
        $aConfVars['str']['sVersion'] = $myConfig->getConfigParam( 'sVersion' );

        $this->_aViewData["var_constraints"] = $aDbVariables['constraints'];
        $this->_aViewData["var_grouping"]    = $aDbVariables['grouping'];
        foreach ($this->_aConfParams as $sType => $sParam) {
            $this->_aViewData[$sParam] = $aConfVars[$sType];
        }

        // #251A passing country list
        $oCountryList = oxNew( "oxCountryList" );
        $oCountryList->loadActiveCountries( oxRegistry::getLang()->getObjectTplLanguage() );
        if ( isset($aConfVars['arr']["aHomeCountry"]) && count($aConfVars['arr']["aHomeCountry"]) && count($oCountryList)) {
            foreach ( $oCountryList as $sCountryId => $oCountry) {
                if ( in_array($oCountry->oxcountry__oxid->value, $aConfVars['arr']["aHomeCountry"]))
                    $oCountryList[$sCountryId]->selected = "1";
            }
        }

        $this->_aViewData["countrylist"] = $oCountryList;

        // checking if cUrl is enabled
        $this->_aViewData["blCurlIsActive"] = ( !function_exists('curl_init') ) ? false : true;

        return $this->_sThisTemplate;
    }

    /**
     * return theme filter for config variables
     *
     * @return string
     */
    protected function _getModuleForConfigVars()
    {
        return '';
    }

    /**
     * Saves shop configuration variables
     *
     * @return null
     */
    public function saveConfVars()
    {
        $myConfig = $this->getConfig();


        $sShopId = $this->getEditObjectId();
        $sModule = $this->_getModuleForConfigVars();
        foreach ($this->_aConfParams as $sType => $sParam) {
            $aConfVars = oxConfig::getParameter($sParam);
            if (is_array($aConfVars)) {
                foreach ( $aConfVars as $sName => $sValue ) {
                    $oldValue = $myConfig->getConfigParam( $sName );
                    if ( $sValue !== $oldValue  ) {
                        $myConfig->saveShopConfVar(
                                $sType,
                                $sName,
                                $this->_serializeConfVar($sType, $sName, $sValue),
                                $sShopId,
                                $sModule
                        );
                    }
                }
            }
        }
    }

    /**
     * Saves changed shop configuration parameters.
     *
     * @return mixed
     */
    public function save()
    {
        // saving config params
        $this->saveConfVars();

        //saving additional fields ("oxshops__oxdefcat"") that goes directly to shop (not config)
        $oShop = oxNew( "oxshop" );
        if ( $oShop->load( $this->getEditObjectId() ) ) {
            $oShop->assign( oxConfig::getParameter( "editval" ) );
            $oShop->save();
        }
    }

    /**
     * Load and parse config vars from db.
     * Return value is a map:
     *      'vars'        => config variable values as array[type][name] = value
     *      'constraints' => constraints list as array[name] = constraint
     *      'grouping'    => grouping info as array[name] = grouping
     *
     * @param string $sShopId Shop id
     * @param string $sModule module to load (empty string is for base values)
     *
     * @deprecated since v5.0.0 (2012-10-19); Use public loadConfVars().
     *
     * @return array
     */
    public function _loadConfVars($sShopId, $sModule)
    {
        return $this->loadConfVars($sShopId, $sModule);
    }

    /**
     * Load and parse config vars from db.
     * Return value is a map:
     *      'vars'        => config variable values as array[type][name] = value
     *      'constraints' => constraints list as array[name] = constraint
     *      'grouping'    => grouping info as array[name] = grouping
     *
     * @param string $sShopId Shop id
     * @param string $sModule module to load (empty string is for base values)
     *
     * @return array
     */
    public function loadConfVars($sShopId, $sModule)
    {
        $myConfig  = $this->getConfig();
        $aConfVars = array(
            "bool"    => array(),
            "str"     => array(),
            "arr"     => array(),
            "aarr"    => array(),
            "select"  => array(),
        );
        $aVarConstraints = array();
        $aGrouping       = array();
        $oDb = oxDb::getDb();
        $rs = $oDb->Execute(
                "select cfg.oxvarname,
                        cfg.oxvartype,
                        DECODE( cfg.oxvarvalue, ".$oDb->quote( $myConfig->getConfigParam( 'sConfigKey' ) ).") as oxvarvalue,
                        disp.oxvarconstraint,
                        disp.oxgrouping
                from oxconfig as cfg
                    left join oxconfigdisplay as disp
                        on cfg.oxmodule=disp.oxcfgmodule and cfg.oxvarname=disp.oxcfgvarname
                where cfg.oxshopid = '$sShopId'
                    and cfg.oxmodule=".$oDb->quote($sModule)."
                order by disp.oxpos, cfg.oxvarname"
        );

        if ($rs != false && $rs->recordCount() > 0) {
            while (!$rs->EOF) {
                list($sName, $sType, $sValue, $sConstraint, $sGrouping) = $rs->fields;
                $aConfVars[$sType][$sName] = $this->_unserializeConfVar($sType, $sName, $sValue);
                $aVarConstraints[$sName]   = $this->_parseConstraint( $sType, $sConstraint );
                if ($sGrouping) {
                    if (!isset($aGrouping[$sGrouping])) {
                        $aGrouping[$sGrouping] = array($sName=>$sType);
                    } else {
                        $aGrouping[$sGrouping][$sName] = $sType;
                    }
                }
                $rs->moveNext();
            }
        }

        return array(
            'vars'        => $aConfVars,
            'constraints' => $aVarConstraints,
            'grouping'    => $aGrouping,
        );
    }

    /**
     * parse constraint from type and serialized values
     *
     * @param string $sType       variable type
     * @param string $sConstraint serialized constraint
     *
     * @return mixed
     */
    protected function _parseConstraint($sType, $sConstraint)
    {
        switch ($sType) {
            case "select":
                return array_map('trim', explode('|', $sConstraint));
                break;
        }
        return null;
    }

    /**
     * serialize constraint from type and value
     *
     * @param string $sType       variable type
     * @param mixed  $sConstraint constraint value
     *
     * @return string
     */
    protected function _serializeConstraint($sType, $sConstraint)
    {
        switch ($sType) {
            case "select":
                return implode('|', array_map('trim', $sConstraint));
                break;
        }
        return '';
    }

    /**
     * Unserialize config var depending on it's type
     *
     * @param string $sType  var type
     * @param string $sName  var name
     * @param string $sValue var value
     *
     * @return mixed
     */
    public function _unserializeConfVar($sType, $sName, $sValue)
    {
        $oStr = getStr();
        $mData = null;

        switch ($sType) {
            case "bool":
                $mData = ($sValue == "true" || $sValue == "1");
                break;

            case "str":
            case "select":
            case "num":
            case "int":
                $mData = $oStr->htmlentities( $sValue );
                if (in_array($sName, $this->_aParseFloat)) {
                    $mData = str_replace( ',', '.', $mData );
                }
                break;

            case "arr":
                if (in_array($sName, $this->_aSkipMultiline)) {
                    $mData = unserialize( $sValue );
                } else {
                    $mData = $oStr->htmlentities( $this->_arrayToMultiline( unserialize( $sValue ) ) );
                }
                break;

            case "aarr":
                if (in_array($sName, $this->_aSkipMultiline)) {
                    $mData = unserialize( $sValue );
                } else {
                    $mData = $oStr->htmlentities( $this->_aarrayToMultiline( unserialize( $sValue ) ) );
                }
                break;
        }
        return $mData;
    }

    /**
     * Serialize config var depending on it's type
     *
     * @param string $sType  var type
     * @param string $sName  var name
     * @param mixed  $mValue var value
     *
     * @return string
     */
    public function _serializeConfVar($sType, $sName, $mValue)
    {
        $sData = $mValue;

        switch ($sType) {
            case "bool":
                break;

            case "str":
            case "select":
            case "int":
                if (in_array($sName, $this->_aParseFloat)) {
                    $sData = str_replace( ',', '.', $sData );
                }
                break;

            case "arr":
                if ( !is_array( $mValue ) ) {
                    $sData = $this->_multilineToArray( $mValue );
                }
                break;

            case "aarr":
                $sData = $this->_multilineToAarray( $mValue );
                break;
        }
        return $sData;
    }

    /**
     * Converts simple array to multiline text. Returns this text.
     *
     * @param array $aInput Array with text
     *
     * @return string
     */
    protected function _arrayToMultiline( $aInput )
    {
        $sVal = '';
        if ( is_array( $aInput ) ) {
            $sVal = implode( "\n", $aInput );
        }
        return $sVal;
    }

    /**
     * Converts Multiline text to simple array. Returns this array.
     *
     * @param string $sMultiline Multiline text
     *
     * @return array
     */
    protected function _multilineToArray( $sMultiline )
    {
        $aArr = explode( "\n", $sMultiline );
        if ( is_array( $aArr ) ) {
            foreach ( $aArr as $sKey => $sVal ) {
                $aArr[$sKey] = trim( $sVal );
                if ( $aArr[$sKey] == "" ) {
                    unset( $aArr[$sKey] );
                }
            }
            return $aArr;
        }
    }

    /**
     * Converts associative array to multiline text. Returns this text.
     *
     * @param array $aInput Array to convert
     *
     * @return string
     */
    protected function _aarrayToMultiline( $aInput )
    {
        if ( is_array( $aInput ) ) {
            $sMultiline = '';
            foreach ( $aInput as $sKey => $sVal ) {
                if ( $sMultiline ) {
                    $sMultiline .= "\n";
                }
                $sMultiline .= $sKey." => ".$sVal;
            }
            return $sMultiline;
        }
    }

    /**
     * Converts Multiline text to associative array. Returns this array.
     *
     * @param string $sMultiline Multiline text
     *
     * @return array
     */
    protected function _multilineToAarray( $sMultiline )
    {
        $oStr = getStr();
        $aArr = array();
        $aLines = explode( "\n", $sMultiline );
        foreach ( $aLines as $sLine ) {
            $sLine = trim( $sLine );
            if ( $sLine != "" && $oStr->preg_match( "/(.+)=>(.+)/", $sLine, $aRegs ) ) {
                $sKey = trim( $aRegs[1] );
                $sVal = trim( $aRegs[2] );
                if ( $sKey != "" && $sVal != "" ) {
                    $aArr[$sKey] = $sVal;
                }
            }
        }

        return $aArr;
    }

    /**
     * Returns active/editable object id
     *
     * @return string
     */
    public function getEditObjectId()
    {
        $sEditId = parent::getEditObjectId();
        if ( !$sEditId ) {
            return $this->getConfig()->getShopId();
        } else {
            return $sEditId;
        }
    }

}
