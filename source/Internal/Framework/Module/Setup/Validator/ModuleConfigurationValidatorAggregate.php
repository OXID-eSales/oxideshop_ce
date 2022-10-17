<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

class ModuleConfigurationValidatorAggregate implements ModuleConfigurationValidatorInterface
{
    private array $validators;

    public function __construct(ModuleConfigurationValidatorInterface ...$validators)
    {
        $this->validators = $validators;
    }

    public function validate(ModuleConfiguration $configuration, int $shopId): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($configuration, $shopId);
        }
    }
}
