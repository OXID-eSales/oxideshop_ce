<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

interface TemplateRendererBridgeInterface
{
    /**
     * @return TemplateRendererInterface
     */
    public function getTemplateRenderer(): TemplateRendererInterface;

    /**
     * @param mixed $engine
     */
    public function setEngine($engine);

    /**
     * @return mixed
     */
    public function getEngine();
}
