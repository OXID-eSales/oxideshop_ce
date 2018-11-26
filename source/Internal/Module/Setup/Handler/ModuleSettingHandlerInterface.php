<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongSettingModuleSettingHandlerException;

/**
 * @internal
 */
interface ModuleSettingHandlerInterface
{
    /**
     * @param ModuleSetting $moduleSetting
     * @param string        $moduleId
     * @param int           $shopId
     *
     * @throws WrongSettingModuleSettingHandlerException
     */
    public function handleOnModuleActivation(ModuleSetting $moduleSetting, string $moduleId, int $shopId);

    /**
     * @param ModuleSetting $moduleSetting
     * @param string        $moduleId
     * @param int           $shopId
     *
     * @throws WrongSettingModuleSettingHandlerException
     */
    public function handleOnModuleDeactivation(ModuleSetting $moduleSetting, string $moduleId, int $shopId);

    /**
     * @param ModuleSetting $moduleSetting
     * @return bool
     */
    public function canHandle(ModuleSetting $moduleSetting): bool;
}
