<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

/**
 * Interface TemplateEngineConfigurationInterface
 *
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
interface TemplateEngineConfigurationInterface
{
    /**
     * @return array
     */
    public function getParameters();
}