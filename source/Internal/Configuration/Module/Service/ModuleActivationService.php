<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Service;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfigurationIdentifier;

/**
 * @internal
 */
class ModuleActivationService implements ModuleActivationServiceInterface
{
    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * @param string $moduleName
     * @param int    $shopId
     */
    public function activate(string $moduleName, int $shopId)
    {
        // TODO: Implement activate() method.
    }

    /**
     * @param string $moduleName
     * @param int    $shopId
     */
    public function deactivate(string $moduleName, int $shopId)
    {
        // TODO: Implement deactivate() method.
    }
}
