<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Internal\Common\FormConfiguration\FieldConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Form\ContactForm\ContactFormBridgeInterface;
use Exception;

/**
 * Admin shop config manager.
 * Collects shop config information, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> General.
 */
class ShopConfiguration extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    protected $_sThisTemplate = 'shop_config.tpl';
    protected $_aSkipMultiline = ['aHomeCountry'];
    protected $_aParseFloat = ['iMinOrderPrice'];

    protected $_aConfParams = [
        "bool"   => 'confbools',
        "str"    => 'confstrs',
        "arr"    => 'confarrs',
        "aarr"   => 'confaarrs',
        "select" => 'confselects',
        "num"    => 'confnum',
    ];

    /**
     * Executes parent method parent::render(), passes shop configuration parameters
     * to Smarty and returns name of template file "shop_config.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $this->_aViewData["edit"] = $oShop = $this->_getEditShop($soxId);

            try {
                // category choosen as default
                $this->_aViewData["defcat"] = null;
                if ($oShop->oxshops__oxdefcat->value) {
                    $oCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
                    if ($oCat->load($oShop->oxshops__oxdefcat->value)) {
                        $this->_aViewData["defcat"] = $oCat;
                    }
                }
            } catch (Exception $oExcp) {
                // on most cases this means that views are broken, so just
                // outputting notice and keeping functionality flow ..
                $this->_aViewData["updateViews"] = 1;
            }

            $iAoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc");
            if ($iAoc == 1) {
                $oShopDefaultCategoryAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ShopDefaultCategoryAjax::class);
                $this->_aViewData['oxajax'] = $oShopDefaultCategoryAjax->getColumns();

                return "popups/shop_default_category.tpl";
            }
        }

        $aDbVariables = $this->loadConfVars($soxId, $this->_getModuleForConfigVars());
        $aConfVars = $aDbVariables['vars'];
        $aConfVars['str']['sVersion'] = $myConfig->getConfigParam('sVersion');

        $this->_aViewData["var_constraints"] = $aDbVariables['constraints'];
        $this->_aViewData["var_grouping"] = $aDbVariables['grouping'];
        foreach ($this->_aConfParams as $sType => $sParam) {
            $this->_aViewData[$sParam] = $aConfVars[$sType];
        }

        // #251A passing country list
        $oCountryList = oxNew(\OxidEsales\Eshop\Application\Model\CountryList::class);
        $oCountryList->loadActiveCountries(\OxidEsales\Eshop\Core\Registry::getLang()->getObjectTplLanguage());
        if (isset($aConfVars['arr']["aHomeCountry"]) && count($aConfVars['arr']["aHomeCountry"]) && count($oCountryList)) {
            foreach ($oCountryList as $sCountryId => $oCountry) {
                if (in_array($oCountry->oxcountry__oxid->value, $aConfVars['arr']["aHomeCountry"])) {
                    $oCountryList[$sCountryId]->selected = "1";
                }
            }
        }

        $this->_aViewData["countrylist"] = $oCountryList;

        // checking if cUrl is enabled
        $this->_aViewData["blCurlIsActive"] = (!function_exists('curl_init')) ? false : true;

        /** @var ContactFormBridgeInterface $contactFormBridge */
        $contactFormBridge = $this->getContainer()->get(ContactFormBridgeInterface::class);
        $contactFormConfiguration = $contactFormBridge->getContactFormConfiguration();

        /** @var FieldConfigurationInterface $fieldConfiguration */
        foreach ($contactFormConfiguration->getFieldConfigurations() as $fieldConfiguration) {
            $this->_aViewData['contactFormFieldConfigurations'][] = [
                'name' => $fieldConfiguration->getName(),
                'label' => $fieldConfiguration->getLabel(),
                'isRequired' => $fieldConfiguration->isRequired(),
            ];
        }

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
     */
    public function saveConfVars()
    {
        $myConfig = $this->getConfig();

        $this->resetContentCache();

        $sShopId = $this->getEditObjectId();
        $sModule = $this->_getModuleForConfigVars();

        $configValidator = oxNew(\OxidEsales\Eshop\Core\NoJsValidator::class);
        foreach ($this->_aConfParams as $sType => $sParam) {
            $aConfVars = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter($sParam, true);
            if (is_array($aConfVars)) {
                foreach ($aConfVars as $sName => $sValue) {
                    $oldValue = $myConfig->getConfigParam($sName);
                    if ($sValue !== $oldValue) {
                        $sValueToValidate = is_array($sValue) ? join(', ', $sValue) : $sValue;
                        if (!$configValidator->isValid($sValueToValidate)) {
                            $error = oxNew(\OxidEsales\Eshop\Core\DisplayError::class);
                            $error->setFormatParameters(htmlspecialchars($sValueToValidate));
                            $error->setMessage("SHOP_CONFIG_ERROR_INVALID_VALUE");
                            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($error);
                            continue;
                        }
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
     */
    public function save()
    {
        // saving config params
        $this->saveConfVars();

        //saving additional fields ("oxshops__oxdefcat"") that goes directly to shop (not config)
        /** @var \OxidEsales\Eshop\Application\Model\Shop $oShop */
        $oShop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        if ($oShop->load($this->getEditObjectId())) {
            $oShop->assign(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval"));
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
     * @return array
     */
    public function loadConfVars($sShopId, $sModule)
    {
        $myConfig = $this->getConfig();
        $aConfVars = [
            "bool"   => [],
            "str"    => [],
            "arr"    => [],
            "aarr"   => [],
            "select" => [],
        ];
        $aVarConstraints = [];
        $aGrouping = [];
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $rs = $oDb->select(
            "select cfg.oxvarname,
                    cfg.oxvartype,
                    DECODE( cfg.oxvarvalue, " . $oDb->quote($myConfig->getConfigParam('sConfigKey')) . ") as oxvarvalue,
                        disp.oxvarconstraint,
                        disp.oxgrouping
                from oxconfig as cfg
                    left join oxconfigdisplay as disp
                        on cfg.oxmodule=disp.oxcfgmodule and cfg.oxvarname=disp.oxcfgvarname
                where cfg.oxshopid = '$sShopId'
                    and cfg.oxmodule=" . $oDb->quote($sModule) . "
                order by disp.oxpos, cfg.oxvarname"
        );

        if ($rs != false && $rs->count() > 0) {
            while (!$rs->EOF) {
                list($sName, $sType, $sValue, $sConstraint, $sGrouping) = $rs->fields;
                $aConfVars[$sType][$sName] = $this->_unserializeConfVar($sType, $sName, $sValue);
                $aVarConstraints[$sName] = $this->_parseConstraint($sType, $sConstraint);
                if ($sGrouping) {
                    if (!isset($aGrouping[$sGrouping])) {
                        $aGrouping[$sGrouping] = [$sName => $sType];
                    } else {
                        $aGrouping[$sGrouping][$sName] = $sType;
                    }
                }
                $rs->fetchRow();
            }
        }

        return [
            'vars'        => $aConfVars,
            'constraints' => $aVarConstraints,
            'grouping'    => $aGrouping,
        ];
    }

    /**
     * If allow to configure information sending to OXID.
     * For PE and EE users it is always turned on.
     *
     * @return bool
     */
    public function informationSendingToOxidConfigurable()
    {
        $facts = new \OxidEsales\Facts\Facts();
        if (!$facts->isCommunity()) {
            return false;
        }

        return true;
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
                $mData = $oStr->htmlentities($sValue);
                if (in_array($sName, $this->_aParseFloat)) {
                    $mData = str_replace(',', '.', $mData);
                }
                break;

            case "arr":
                if (in_array($sName, $this->_aSkipMultiline)) {
                    $mData = unserialize($sValue);
                } else {
                    $mData = $oStr->htmlentities($this->_arrayToMultiline(unserialize($sValue)));
                }
                break;

            case "aarr":
                if (in_array($sName, $this->_aSkipMultiline)) {
                    $mData = unserialize($sValue);
                } else {
                    $mData = $oStr->htmlentities($this->_aarrayToMultiline(unserialize($sValue)));
                }
                break;
        }

        return $mData;
    }

    /**
     * Prepares data for storing to database.
     * Example: $sType='aarr', $sName='aModules', $mValue='key1=>val1\nkey2=>val2'
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
                    $sData = str_replace(',', '.', $sData);
                }
                break;

            case "arr":
                if (!is_array($mValue)) {
                    $sData = $this->_multilineToArray($mValue);
                }
                break;

            case "aarr":
                $sData = $this->_multilineToAarray($mValue);
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
    protected function _arrayToMultiline($aInput)
    {
        return implode("\n", (array) $aInput);
    }

    /**
     * Converts Multiline text to simple array. Returns this array.
     *
     * @param string $sMultiline Multiline text
     *
     * @return array
     */
    protected function _multilineToArray($sMultiline)
    {
        $aArr = explode("\n", $sMultiline);
        if (is_array($aArr)) {
            foreach ($aArr as $sKey => $sVal) {
                $aArr[$sKey] = trim($sVal);
                if ($aArr[$sKey] == "") {
                    unset($aArr[$sKey]);
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
    protected function _aarrayToMultiline($aInput)
    {
        if (is_array($aInput)) {
            $sMultiline = '';
            foreach ($aInput as $sKey => $sVal) {
                if ($sMultiline) {
                    $sMultiline .= "\n";
                }
                $sMultiline .= $sKey . " => " . $sVal;
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
    protected function _multilineToAarray($sMultiline)
    {
        $oStr = getStr();
        $aArr = [];
        $aLines = explode("\n", $sMultiline);
        foreach ($aLines as $sLine) {
            $sLine = trim($sLine);
            if ($sLine != "" && $oStr->preg_match("/(.+)=>(.+)/", $sLine, $aRegs)) {
                $sKey = trim($aRegs[1]);
                $sVal = trim($aRegs[2]);
                if ($sKey != "" && $sVal != "") {
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
        if (!$sEditId) {
            return $this->getConfig()->getShopId();
        }

        return $sEditId;
    }
}
