<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter;

use OxidEsales\Eshop\Core\MailValidator;
use OxidEsales\Eshop\Core\Module\ModuleList;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Module\ModuleCache;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Adapter\Exception\ModuleNotLoadableException;

/**
 * @internal
 */
class ShopAdapter implements ShopAdapterInterface
{
    /**
     * @param string $email
     *
     * @return bool
     */
    public function isValidEmail($email): bool
    {
        $emailValidator = oxNew(MailValidator::class);

        return $emailValidator->isValidEmail($email);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function translateString($string): string
    {
        $lang = Registry::getLang();

        return $lang->translateString($string);
    }

    /**
     * @param string $moduleId
     *
     * @throws ModuleNotLoadableException
     */
    public function invalidateModuleCache(string $moduleId)
    {
        $module = oxNew(Module::class);
        if (!$module->load($moduleId)) {
            throw new ModuleNotLoadableException('The following module could not be loaded. ModuleId: ' . $moduleId);
        }

        $moduleCache = oxNew(ModuleCache::class, $module);
        $moduleCache->resetCache();
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
}
