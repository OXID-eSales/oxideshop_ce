<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

interface ModuleConfigurationValidatorInterface
{
    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     */
    public function validate(ModuleConfiguration $configuration, int $shopId);
}
