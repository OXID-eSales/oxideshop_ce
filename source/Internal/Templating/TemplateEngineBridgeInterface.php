<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

/**
 * Interface TemplateEngineBridgeInterface
 */
interface TemplateEngineBridgeInterface
{
    /**
     * @param string $name The template name
     *
     * @return bool
     */
    public function exists($name);

    /**
     * @return BaseEngineInterface
     */
    public function getEngine();

    /**
     * @param string $templateName The template name
     * @param array  $viewData     An array of parameters to pass to the template
     * @param string $cacheId      The id for template caching
     *
     * @return string
     */
    public function renderTemplate($templateName, $viewData, $cacheId = null);
}
