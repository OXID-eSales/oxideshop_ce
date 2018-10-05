<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

/**
 * Class TemplateEngineBridge
 */
class TemplateEngineBridge implements TemplateEngineBridgeInterface
{
    /**
     * @var BaseEngineInterface
     */
    private $templateEngine;

    /**
     * TemplateEngineBridge constructor.
     *
     * @param BaseEngineInterface $templateEngine
     */
    public function __construct(BaseEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * Checks if file exists.
     *
     * @param string $name The template name
     *
     * @return bool
     */
    public function exists($name)
    {
        return $this->templateEngine->exists($name);
    }

    /**
     * @return BaseEngineInterface
     */
    public function getEngine()
    {
        return $this->templateEngine;
    }

    /**
     * @param string $templateName The template name
     * @param array  $viewData     An array of parameters to pass to the template
     * @param string $cacheId      The id for template caching
     *
     * @return string
     */
    public function renderTemplate($templateName, $viewData, $cacheId = null)
    {
        $templating = $this->templateEngine;
        $templating->setCacheId($cacheId);

        return $templating->render($templateName, $viewData);
    }
}
