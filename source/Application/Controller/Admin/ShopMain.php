<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxException;

/**
 * Admin article main shop manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Main Menu -> Core Settings -> Main.
 */
class ShopMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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
            $shop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
            $subjLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("subjlang");
            if (!isset($subjLang)) {
                $subjLang = $this->_iEditLang;
            }

            if ($subjLang && $subjLang > 0) {
                $this->_aViewData["subjlang"] = $subjLang;
            }

            $shop->loadInLang($subjLang, $shopId);

            $this->_aViewData["edit"] = $shop;
            //\OxidEsales\Eshop\Core\Session::setVar( "actshop", $soxId);//echo "<h2>$soxId</h2>";
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("shp", $shopId);
        }

        $this->checkParent($shop);

        $this->_aViewData['IsOXDemoShop'] = $config->isDemoShop();
        if (!isset($this->_aViewData['updatenav'])) {
            $this->_aViewData['updatenav'] = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('updatenav');
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

        $parameters = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $user = $this->getUser();
        $shopId = $this->updateShopIdByUser($user, $shopId, false);

        //  #918 S
        // checkbox handling
        $parameters['oxshops__oxactive'] = (isset($parameters['oxshops__oxactive']) && $parameters['oxshops__oxactive'] == true) ? 1 : 0;
        $parameters['oxshops__oxproductive'] = (isset($parameters['oxshops__oxproductive']) && $parameters['oxshops__oxproductive'] == true) ? 1 : 0;

        $subjLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("subjlang");
        $shopLanguageId = ($subjLang && $subjLang > 0) ? $subjLang : 0;

        $shop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
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

        if (($newSMPTPass = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("oxsmtppwd"))) {
            $shop->oxshops__oxsmtppwd->setValue($newSMPTPass == '-' ? "" : $newSMPTPass);
        }

        $canCreateShop = $this->canCreateShop($shopId, $shop, $config);
        if (!$canCreateShop) {
            return;
        }

        try {
            $shop->save();
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $e) {
            $this->checkExceptionType($e);
            return;
        }

        $this->_aViewData["updatelist"] = "1";

        $this->updateShopInformation($config, $shop, $shopId);

        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("actshop", $shopId);
    }

    /**
     * Returns array of config variables which cannot be copied
     *
     * @return array
     */
    protected function _getNonCopyConfigVars()
    {
        $nonCopyVars = [
            'aSerials',
            'IMS',
            'IMD',
            'IMA',
            'sBackTag',
            'sUtilModule',
            'aModulePaths',
            'aModuleFiles',
            'aModuleEvents',
            'aModuleVersions',
            'aModuleTemplates',
            'aModules',
            'aDisabledModules',
            'aModuleExtensions',
            'aModuleControllers',
            'moduleSmartyPluginDirectories',
            'activeModules',
        ];
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
     * @param \OxidEsales\Eshop\Application\Model\Shop $shop new shop object
     */
    protected function _copyConfigVars($shop)
    {
        $config = $this->getConfig();
        $utilsObject = \OxidEsales\Eshop\Core\Registry::getUtilsObject();
        $db = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

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
     * @param \OxidEsales\Eshop\Application\Model\User $user
     * @param string                                   $shopId
     * @param bool                                     $updateViewData If needs to update view data when shop Id changes.
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
     * @param \OxidEsales\Eshop\Application\Model\Shop $shop
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
     * @param \OxidEsales\Eshop\Core\Exception\StandardException $exception
     */
    protected function checkExceptionType($exception)
    {
    }

    /**
     * Check if Shop can be created.
     *
     * @param string                                   $shopId
     * @param \OxidEsales\Eshop\Application\Model\Shop $shop
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
     * @param \OxidEsales\Eshop\Core\Config            $config
     * @param \OxidEsales\Eshop\Application\Model\Shop $shop
     * @param string                                   $shopId
     */
    protected function updateShopInformation($config, $shop, $shopId)
    {
    }
}
