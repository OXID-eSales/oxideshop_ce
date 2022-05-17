<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator;

/**
 * @deprecated will be removed in v7.0
 */
interface SettingValidatorInterface
{
    /**
     * @param string $metadataVersion
     * @param array  $moduleSettings
     */
    public function validate(string $metadataVersion, array $moduleSettings);
}
