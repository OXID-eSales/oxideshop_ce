<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

class TemplateEngineBridge implements TemplateEngineBridgeInterface
{
    private $templateEngine;

    public function __construct(BaseEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    public function exists($name)
    {
        return $this->templateEngine->exists($name);
    }

    public function getEngine()
    {
        return $this->templateEngine;
    }

    public function renderTemplate($templateName, $viewData, $cacheId = null)
    {
        $templating = $this->templateEngine;
        $templating->setCacheId($cacheId);

        return $templating->render($templateName, $viewData);
    }
}