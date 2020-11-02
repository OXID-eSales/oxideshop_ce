<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

class SmartyTemplateRendererBridge implements TemplateRendererBridgeInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    /**
     * SmartyTemplateRendererBridge constructor.
     */
    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getTemplateRenderer(): TemplateRendererInterface
    {
        return $this->renderer;
    }

    /**
     * @param mixed $engine
     */
    public function setEngine($engine): void
    {
        $this->renderer->getTemplateEngine()->setSmarty($engine);
    }

    /**
     * @return mixed
     */
    public function getEngine()
    {
        return $this->renderer->getTemplateEngine()->getSmarty();
    }
}
