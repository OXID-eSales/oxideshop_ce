<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\Validator;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleSetting;

/**
 * @internal
 */
interface SettingValidatorInterface
{
    /**
     * @param string        $metadataVersion
     * @param ModuleSetting $moduleSetting
     */
    public function validate(string $metadataVersion, ModuleSetting $moduleSetting);
}
