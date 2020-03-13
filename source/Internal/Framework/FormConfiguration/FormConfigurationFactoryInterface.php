<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FormConfiguration;

interface FormConfigurationFactoryInterface
{
    /**
     * @return FormConfigurationInterface
     */
    public function getFormConfiguration();
}
