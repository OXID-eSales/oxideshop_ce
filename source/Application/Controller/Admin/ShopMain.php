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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxUtilsObject;
use oxException;

/**
 * Admin article main shop manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Main Menu -> Core Settings -> Main.
 */
class ShopMain extends \oxAdminDetails
{
    /** Identifies new shop. */
    const NEW_SHOP_ID = "-1";

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
        $config = $this->getConfig();
        parent::render();

        $shopId = $this->_aViewData["oxid"] = $this->getEditObjectId();

        $templateName = $this->renderNewShop();

        if ($templateName) {
            return $templateName;
        }

        $user = $this->getUser();
        $shopId = $this->updateShopIdByUser($user, $shopId, true);

        if (isset($shopId) && $shopId != self::NEW_SHOP_ID) {
            // load object
            $shop = oxNew("oxshop");
            $subjLang = oxRegistry::getConfig()->getRequestParameter("subjlang");
            if (!isset($subjLang)) {
                $subjLang = $this->_iEditLang;
            }

            if ($subjLang && $subjLang > 0) {
                $this->_aViewData["subjlang"] = $subjLang;
            }

            $shop->loadInLang($subjLang, $shopId);

            $this->_aViewData["edit"] = $shop;
            //oxSession::setVar( "actshop", $soxId);//echo "<h2>$soxId</h2>";
            oxRegistry::getSession()->setVariable("shp", $shopId);
        }

        $this->checkParent($shop);

        $this->_aViewData['IsOXDemoShop'] = $config->isDemoShop();
        if (!isset($this->_aViewData['updatenav'])) {
            $this->_aViewData['updatenav'] = oxRegistry::getConfig()->getRequestParameter('updatenav');
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

        $config = $this->getConfig();
        $shopId = $this->getEditObjectId();

        $parameters = oxRegistry::getConfig()->getRequestParameter("editval");

        $user = $this->getUser();
        $shopId = $this->updateShopIdByUser($user, $shopId, false);

        //  #918 S
        // checkbox handling
        $parameters['oxshops__oxactive'] = (isset($parameters['oxshops__oxactive']) && $parameters['oxshops__oxactive'] == true) ? 1 : 0;
        $parameters['oxshops__oxproductive'] = (isset($parameters['oxshops__oxproductive']) && $parameters['oxshops__oxproductive'] == true) ? 1 : 0;

        $subjLang = oxRegistry::getConfig()->getRequestParameter("subjlang");
        $shopLanguageId = ($subjLang && $subjLang > 0) ? $subjLang : 0;

        $shop = oxNew("oxshop");
        if ($shopId != self::NEW_SHOP_ID) {
            $shop->loadInLang($shopLanguageId, $shopId);
        } else {
            $parameters = $this->updateParameters($parameters);
        }

        if ($parameters['oxshops__oxsmtp']) {
            $parameters['oxshops__oxsmtp'] = trim($parameters['oxshops__oxsmtp']);
        }

        $shop->setLanguage(0);
        $shop->assign($parameters);
        $shop->setLanguage($shopLanguageId);

        if (($newSMPTPass = oxRegistry::getConfig()->getRequestParameter("oxsmtppwd"))) {
            $shop->oxshops__oxsmtppwd->setValue($newSMPTPass == '-' ? "" : $newSMPTPass);
        }

        $canCreateShop = $this->canCreateShop($shopId, $shop, $config);
        if (!$canCreateShop) {
            return;
        }

        try {
            $shop->save();
        } catch (oxException $e) {
            $this->checkExceptionType($e);
            return;
        }

        $this->_aViewData["updatelist"] = "1";

        $this->updateShopInformation($config, $shop, $shopId);

        oxRegistry::getSession()->setVariable("actshop", $shopId);
    }

    /**
     * Returns array of config variables which cannot be copied
     *
     * @return array
     */
    protected function _getNonCopyConfigVars()
    {
        $nonCopyVars = array("aSerials", "IMS", "IMD", "IMA", "sBackTag", "sUtilModule", "aModulePaths", "aModuleFiles", "aModuleEvents", "aModuleVersions", "aModuleTemplates", "aModules", "aDisabledModules");
        //adding non copable multishop field options
        $multiShopTables = $this->getConfig()->getConfigParam('aMultiShopTables');
        foreach ($multiShopTables as $multishopTable) {
            $nonCopyVars[] = 'blMallInherit_' . strtolower($multishopTable);
        }

        return $nonCopyVars;
    }

    /**
     * Copies base shop config variables to current
     *
     * @param oxshop $shop new shop object
     */
    protected function _copyConfigVars($shop)
    {
        $config = $this->getConfig();
        $utilsObject = oxUtilsObject::getInstance();
        $db = oxDb::getDb();

        $nonCopyVars = $this->_getNonCopyConfigVars();

        $selectShopConfigurationQuery =
            "select oxvarname, oxvartype,
            DECODE( oxvarvalue, " . $db->quote($config->getConfigParam('sConfigKey')) . ") as oxvarvalue, oxmodule
            from oxconfig where oxshopid = '1'";
        $shopConfiguration = $db->select($selectShopConfigurationQuery);
        if ($shopConfiguration != false && $shopConfiguration->count() > 0) {
            while (!$shopConfiguration->EOF) {
                $configName = $shopConfiguration->fields[0];
                if (!in_array($configName, $nonCopyVars)) {
                    $newId = $utilsObject->generateUID();
                    $insertNewConfigQuery =
                        "insert into oxconfig
                        (oxid, oxshopid, oxvarname, oxvartype, oxvarvalue, oxmodule) values ( '$newId', " . $db->quote($shop->getId())
                        . ", " . $db->quote($shopConfiguration->fields[0])
                        . ", " . $db->quote($shopConfiguration->fields[1])
                        . ",  ENCODE( " . $db->quote($shopConfiguration->fields[2])
                        . ", '" . $config->getConfigParam('sConfigKey')
                        . "')"
                        . ", " . $db->quote($shopConfiguration->fields[3]) . " )";
                    $db->execute($insertNewConfigQuery);
                }
                $shopConfiguration->fetchRow();
            }
        }

        $inheritAll = $shop->oxshops__oxisinherited->value ? "true" : "false";
        $multiShopTables = $config->getConfigParam('aMultiShopTables');
        foreach ($multiShopTables as $multishopTable) {
            $config->saveShopConfVar("bool", 'blMallInherit_' . strtolower($multishopTable), $inheritAll, $shop->oxshops__oxid->value);
        }
    }

    /**
     * Return template name for new shop if it is different from standard.
     *
     * @return string
     */
    protected function renderNewShop()
    {
        return '';
    }

    /**
     * Check user rights and change userId if need.
     *
     * @param oxUser $user
     * @param string $shopId
     * @param bool   $updateViewData If needs to update view data when shop Id changes.
     *
     * @return string
     */
    protected function updateShopIdByUser($user, $shopId, $updateViewData = false)
    {
        return $shopId;
    }

    /**
     * Load Shop parent and set result to _aViewData.
     *
     * @param oxShop $shop
     */
    protected function checkParent($shop)
    {
    }

    /**
     * Unset not used Shop parameters.
     *
     * @param array $parameters
     *
     * @return array
     */
    protected function updateParameters($parameters)
    {
        $parameters['oxshops__oxid'] = null;

        return $parameters;
    }

    /**
     * Check for exception type and set it to _aViewData.
     *
     * @param oxException $exception
     */
    protected function checkExceptionType($exception)
    {
    }

    /**
     * Check if Shop can be created.
     *
     * @param string $shopId
     * @param oxShop $shop
     *
     * @return bool
     */
    protected function canCreateShop($shopId, $shop)
    {
        return true;
    }

    /**
     * Update shop information in DB and oxConfig.
     *
     * @param oxConfig $config
     * @param oxShop   $shop
     * @param string   $shopId
     */
    protected function updateShopInformation($config, $shop, $shopId)
    {
    }
}
