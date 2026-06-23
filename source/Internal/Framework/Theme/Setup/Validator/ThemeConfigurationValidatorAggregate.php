<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

readonly class ThemeConfigurationValidatorAggregate implements ThemeConfigurationValidatorInterface
{
    /**
     * @var ThemeConfigurationValidatorInterface[]
     */
    private array $validators;

    public function __construct(ThemeConfigurationValidatorInterface ...$validators)
    {
        $this->validators = $validators;
    }

    public function validate(ThemeConfiguration $themeConfiguration, int $shopId): void
    {
        foreach ($this->validators as $validator) {
            $validator->validate($themeConfiguration, $shopId);
        }
    }
}
