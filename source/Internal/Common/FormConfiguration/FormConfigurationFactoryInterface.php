<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\FormConfiguration;

/**
 * @internal
 */
interface FormConfigurationFactoryInterface
{
    /**
     * @return FormConfigurationInterface
     */
    public function getFormConfiguration();
}
