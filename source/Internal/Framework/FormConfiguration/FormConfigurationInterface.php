<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration;

interface FormConfigurationInterface
{
    /**
     * @param FieldConfigurationInterface $fieldConfiguration
     * @return self
     */
    public function addFieldConfiguration(FieldConfigurationInterface $fieldConfiguration);

    /**
     * @return array
     */
    public function getFieldConfigurations();
}
