<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration\FieldConfigurationInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Contact\Form\ContactFormBridgeInterface;
use Exception;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleSettingNotFountException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ModuleSettingBridgeInterface;

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
        $config = $this->getConfig();

        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $this->_aViewData["edit"] = $shop = $this->_getEditShop($soxId);

            try {
                // category choosen as default
                $this->_aViewData["defcat"] = null;
                if ($shop->oxshops__oxdefcat->value) {
                    $category = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
                    if ($category->load($shop->oxshops__oxdefcat->value)) {
                        $this->_aViewData["defcat"] = $category;
                    }
                }
            } catch (Exception $exception) {
                // on most cases this means that views are broken, so just
                // outputting notice and keeping functionality flow ..
                $this->_aViewData["updateViews"] = 1;
            }

            $aoc = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aoc");
            if ($aoc == 1) {
                $shopDefaultCategoryAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ShopDefaultCategoryAjax::class);
                $this->_aViewData['oxajax'] = $shopDefaultCategoryAjax->getColumns();

                return "popups/shop_default_category.tpl";
            }
        }

        $dbVariables = $this->loadConfVars($soxId, $this->_getModuleForConfigVars());
        $confVars = $dbVariables['vars'];
        $confVars['str']['sVersion'] = $config->getConfigParam('sVersion');

        $this->_aViewData["var_constraints"] = $dbVariables['constraints'];
        $this->_aViewData["var_grouping"] = $dbVariables['grouping'];
        foreach ($this->_aConfParams as $type => $param) {
            $this->_aViewData[$param] = $confVars[$type];
        }

        // #251A passing country list
        $countryList = oxNew(\OxidEsales\Eshop\Application\Model\CountryList::class);
        $countryList->loadActiveCountries(\OxidEsales\Eshop\Core\Registry::getLang()->getObjectTplLanguage());
        if (isset($confVars['arr']["aHomeCountry"]) && count($confVars['arr']["aHomeCountry"]) && count($countryList)) {
            foreach ($countryList as $sCountryId => $oCountry) {
                if (in_array($oCountry->oxcountry__oxid->value, $confVars['arr']["aHomeCountry"])) {
                    $countryList[$sCountryId]->selected = "1";
                }
            }
        }

        $this->_aViewData["countrylist"] = $countryList;

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
        $config = $this->getConfig();

        $this->resetContentCache();

        $configValidator = oxNew(\OxidEsales\Eshop\Core\NoJsValidator::class);
        foreach ($this->_aConfParams as $existingConfigType => $existingConfigName) {
            $requestValue = \OxidEsales\Eshop\Core\Registry::getConfig()
                ->getRequestParameter($existingConfigName, true);
            if (is_array($requestValue)) {
                foreach ($requestValue as $configName => $newConfigValue) {
                    $oldValue = $config->getConfigParam($configName);
                    if ($newConfigValue !== $oldValue) {
                        $sValueToValidate = is_array($newConfigValue) ? join(', ', $newConfigValue) : $newConfigValue;
                        if (!$configValidator->isValid($sValueToValidate)) {
                            $error = oxNew(\OxidEsales\Eshop\Core\DisplayError::class);
                            $error->setFormatParameters(htmlspecialchars($sValueToValidate));
                            $error->setMessage("SHOP_CONFIG_ERROR_INVALID_VALUE");
                            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($error);
                            continue;
                        }
                        $this->saveSetting($configName, $existingConfigType, $newConfigValue);
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
        /** @var \OxidEsales\Eshop\Application\Model\Shop $shop */
        $shop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        if ($shop->load($this->getEditObjectId())) {
            $shop->assign(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval"));
            $shop->save();
        }
    }

    /**
     * Load and parse config vars from db.
     * Return value is a map:
     *      'vars'        => config variable values as array[type][name] = value
     *      'constraints' => constraints list as array[name] = constraint
     *      'grouping'    => grouping info as array[name] = grouping
     *
     * @param string $shopId Shop id
     * @param string $moduleId module to load (empty string is for base values)
     *
     * @return array
     */
    public function loadConfVars($shopId, $moduleId)
    {
        $config = $this->getConfig();
        $configurationVariables = [
            "bool"   => [],
            "str"    => [],
            "arr"    => [],
            "aarr"   => [],
            "select" => [],
        ];
        $constraints = [];
        $groupings = [];
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $rs = $database->select(
            "select cfg.oxvarname,
                    cfg.oxvartype,
                    DECODE( cfg.oxvarvalue, " . $database->quote($config->getConfigParam('sConfigKey')) . ") as oxvarvalue,
                        disp.oxvarconstraint,
                        disp.oxgrouping
                from oxconfig as cfg
                    left join oxconfigdisplay as disp
                        on cfg.oxmodule=disp.oxcfgmodule and cfg.oxvarname=disp.oxcfgvarname
                where cfg.oxshopid = :oxshopid
                    and cfg.oxmodule = :oxmodule
                order by disp.oxpos, cfg.oxvarname",
            [
                ':oxshopid' => $shopId,
                ':oxmodule' => $moduleId
            ]
        );

        if ($rs != false && $rs->count() > 0) {
            while (!$rs->EOF) {
                list($name, $type, $value, $constraint, $grouping) = $rs->fields;
                $configurationVariables[$type][$name] = $this->_unserializeConfVar($type, $name, $value);
                $constraints[$name] = $this->_parseConstraint($type, $constraint);
                if ($grouping) {
                    if (!isset($groupings[$grouping])) {
                        $groupings[$grouping] = [$name => $type];
                    } else {
                        $groupings[$grouping][$name] = $type;
                    }
                }
                $rs->fetchRow();
            }
        }

        return [
            'vars'        => $configurationVariables,
            'constraints' => $constraints,
            'grouping'    => $groupings,
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
     * @param string $type       variable type
     * @param string $constraint serialized constraint
     *
     * @return mixed
     */
    protected function _parseConstraint($type, $constraint)
    {
        switch ($type) {
            case "select":
                return array_map('trim', explode('|', $constraint));
                break;
        }
        return null;
    }

    /**
     * serialize constraint from type and value
     *
     * @param string $type       variable type
     * @param mixed  $constraint constraint value
     *
     * @return string
     */
    protected function _serializeConstraint($type, $constraint)
    {
        switch ($type) {
            case "select":
                return implode('|', array_map('trim', $constraint));
                break;
        }
        return '';
    }

    /**
     * Unserialize config var depending on it's type
     *
     * @param string $type  var type
     * @param string $name  var name
     * @param string $value var value
     *
     * @return mixed
     */
    public function _unserializeConfVar($type, $name, $value)
    {
        $str = getStr();
        $data = null;

        switch ($type) {
            case "bool":
                $data = ($value == "true" || $value == "1");
                break;

            case "str":
            case "select":
            case "num":
            case "int":
                $data = $str->htmlentities($value);
                if (in_array($name, $this->_aParseFloat)) {
                    $data = str_replace(',', '.', $data);
                }
                break;

            case "arr":
                if (in_array($name, $this->_aSkipMultiline)) {
                    $data = unserialize($value);
                } else {
                    $data = $str->htmlentities($this->_arrayToMultiline(unserialize($value)));
                }
                break;

            case "aarr":
                if (in_array($name, $this->_aSkipMultiline)) {
                    $data = unserialize($value);
                } else {
                    $data = $str->htmlentities($this->_aarrayToMultiline(unserialize($value)));
                }
                break;
        }

        return $data;
    }

    /**
     * Prepares data for storing to database.
     * Example: $sType='aarr', $sName='aModules', $mValue='key1=>val1\nkey2=>val2'
     *
     * @param string $type  var type
     * @param string $name  var name
     * @param mixed  $value var value
     *
     * @return string
     */
    public function _serializeConfVar($type, $name, $value)
    {
        $data = $value;

        switch ($type) {
            case "bool":
                break;

            case "str":
            case "select":
            case "int":
                if (in_array($name, $this->_aParseFloat)) {
                    $data = str_replace(',', '.', $data);
                }
                break;

            case "arr":
                if (!is_array($value)) {
                    $data = $this->_multilineToArray($value);
                }
                break;

            case "aarr":
                $data = $this->_multilineToAarray($value);
                break;
        }

        return $data;
    }

    /**
     * Converts simple array to multiline text. Returns this text.
     *
     * @param array $input Array with text
     *
     * @return string
     */
    protected function _arrayToMultiline($input)
    {
        return implode("\n", (array) $input);
    }

    /**
     * Converts Multiline text to simple array. Returns this array.
     *
     * @param string $multiline Multiline text
     *
     * @return array
     */
    protected function _multilineToArray($multiline)
    {
        $array = explode("\n", $multiline);
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = trim($value);
                if ($array[$key] == "") {
                    unset($array[$key]);
                }
            }

            return $array;
        }
    }

    /**
     * Converts associative array to multiline text. Returns this text.
     *
     * @param array $input Array to convert
     *
     * @return string
     */
    protected function _aarrayToMultiline($input)
    {
        if (is_array($input)) {
            $multiline = '';
            foreach ($input as $key => $value) {
                if ($multiline) {
                    $multiline .= "\n";
                }
                $multiline .= $key . " => " . $value;
            }

            return $multiline;
        }
    }

    /**
     * Converts Multiline text to associative array. Returns this array.
     *
     * @param string $multiline Multiline text
     *
     * @return array
     */
    protected function _multilineToAarray($multiline)
    {
        $string = getStr();
        $array = [];
        $lines = explode("\n", $multiline);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line != "" && $string->preg_match("/(.+)=>(.+)/", $line, $regs)) {
                $key = trim($regs[1]);
                $value = trim($regs[2]);
                if ($key != "" && $value != "") {
                    $array[$key] = $value;
                }
            }
        }

        return $array;
    }

    /**
     * Returns active/editable object id
     *
     * @return string
     */
    public function getEditObjectId()
    {
        $editId = parent::getEditObjectId();
        if (!$editId) {
            return $this->getConfig()->getShopId();
        }

        return $editId;
    }

    /**
     * @param string $configName
     * @param string $existingConfigType
     * @param mixed $configValue
     */
    private function saveSetting(string $configName, string $existingConfigType, $configValue): void
    {
        $shopId = $this->getEditObjectId();
        $module = $this->_getModuleForConfigVars();
        $config = $this->getConfig();
        $preparedConfigValue = $this->_serializeConfVar($existingConfigType, $configName, $configValue);
        if (strpos($module, 'module:') !== false) {
            $moduleId = explode(':', $module)[1];
            $moduleSettingBridge = ContainerFactory::getInstance()
                ->getContainer()
                ->get(ModuleSettingBridgeInterface::class);
            try {
                $moduleSettingBridge->save($configName, $preparedConfigValue, $moduleId);
            } catch (ModuleSettingNotFountException $exception) {
                Registry::getLogger()->warning(
                    "Module \"$moduleId\" setting \"$configName\" is missing in metadata.php or configuration file.",
                    [$exception]
                );
                $config->saveShopConfVar($existingConfigType, $configName, $preparedConfigValue, $shopId, $module);
            }
        } else {
            $config->saveShopConfVar($existingConfigType, $configName, $preparedConfigValue, $shopId, $module);
        }
    }
}
