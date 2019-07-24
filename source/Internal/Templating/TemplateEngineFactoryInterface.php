<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

/**
 * Interface TemplateEngineFactoryInterface
 * @package OxidEsales\EshopCommunity\Internal\Templating
 */
interface TemplateEngineFactoryInterface
{
    /**
     * @return TemplateEngineInterface
     */
    public function getTemplateEngine(): TemplateEngineInterface;
}
