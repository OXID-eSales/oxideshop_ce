<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject;

/**
 * @internal
 */
class ModuleConfiguration
{
    /**
     * @return ModuleSetting
     */
    public function getModuleSetting(): ModuleSetting
    {
        return new ModuleSetting();
    }
}
