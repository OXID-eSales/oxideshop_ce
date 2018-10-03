<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter;

use OxidEsales\Eshop\Core\MailValidator;
use OxidEsales\Eshop\Core\Module\ModuleList;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Console\ShopSwitchException;
use OxidEsales\Facts\Facts;

/**
 * @internal
 */
class ShopAdapter implements ShopAdapterInterface
{
    /**
     * @param string $email
     * @return bool
     */
    public function isValidEmail($email)
    {
        $emailValidator = oxNew(MailValidator::class);

        return $emailValidator->isValidEmail($email);
    }

    /**
     * @param string $string
     * @return string
     */
    public function translateString($string)
    {
        $lang = Registry::getLang();

        return $lang->translateString($string);
    }

    /**
     * @return array
     */
    public function getModules()
    {
        $moduleList = oxNew(ModuleList::class);
        $moduleList->getModulesFromDir(Registry::getConfig()->getModulesDir());

        return $moduleList->getList();
    }

    /**
     * Switch to subshop by defined ID.
     *
     * @param int $shopId
     * @throws ShopSwitchException
     */
    public function switchToShop(int $shopId)
    {
        if ((new Facts())->isEnterprise()) {
            $_POST['shp'] = $shopId;
            $_POST['actshop'] = $shopId;
            $keepThese = [\OxidEsales\Eshop\Core\ConfigFile::class, 'logger'];
            $registryKeys = Registry::getKeys();
            foreach ($registryKeys as $key) {
                if (in_array($key, $keepThese)) {
                    continue;
                }
                Registry::set($key, null);
            }
            $utilsObject = new \OxidEsales\Eshop\Core\UtilsObject;
            $utilsObject->resetInstanceCache();
            Registry::set(\OxidEsales\Eshop\Core\UtilsObject::class, $utilsObject);
            \OxidEsales\Eshop\Core\Module\ModuleVariablesLocator::resetModuleVariables();
            Registry::getSession()->setVariable('shp', $shopId);
            // Ensure we get rid of all instances of config, even the one in Core\Base
            Registry::set(\OxidEsales\Eshop\Core\Config::class, null);
            Registry::getConfig()->setConfig(null);
            Registry::set(\OxidEsales\Eshop\Core\Config::class, null);
            $moduleVariablesCache = new \OxidEsales\Eshop\Core\FileCache();
            $shopIdCalculator = new \OxidEsales\Eshop\Core\ShopIdCalculator($moduleVariablesCache);
            if (($shopId !== $shopIdCalculator->getShopId()) ||
                ($shopId !== Registry::getConfig()->getShopId())) {
                throw new ShopSwitchException('Failed to switch to subshop with id - ' . $shopId . '.'
                . ' Does this subshop exists?');
            }
        }
    }
}
