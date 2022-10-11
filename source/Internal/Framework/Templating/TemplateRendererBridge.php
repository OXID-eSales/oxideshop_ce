<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

class TemplateRendererBridge implements TemplateRendererBridgeInterface
{
    public function __construct(private TemplateRendererInterface $renderer)
    {
    }

    public function getTemplateRenderer(): TemplateRendererInterface
    {
        return $this->renderer;
    }

    public function setEngine($engine)
    {
    }

    public function getEngine()
    {
    }
}
