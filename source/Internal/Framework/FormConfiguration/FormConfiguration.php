<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration;

class FormConfiguration implements FormConfigurationInterface
{
    /**
     * @var array
     */
    private $fieldConfigurations = [];

    /**
     * @param FieldConfigurationInterface $fieldConfiguration
     * @return self
     */
    public function addFieldConfiguration(FieldConfigurationInterface $fieldConfiguration)
    {
        $this->fieldConfigurations[] = $fieldConfiguration;
        return $this;
    }

    /**
     * @return array
     */
    public function getFieldConfigurations()
    {
        return $this->fieldConfigurations;
    }
}
