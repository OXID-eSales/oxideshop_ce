<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

interface TemplateRendererBridgeInterface
{
    public function getTemplateRenderer(): TemplateRendererInterface;

    /**
     * @deprecated since 7.0.0 will be removed in next major
     */
    public function setEngine($engine);

    /**
     * @deprecated since 7.0.0 will be removed in next major
     */
    public function getEngine();
}
