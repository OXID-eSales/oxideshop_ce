<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Templating;

use Symfony\Component\Templating\EngineInterface;

interface TemplateEngineBridgeInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function exists($name);

    /**
     * @return BaseEngineInterface
     */
    public function getEngine();

    public function renderTemplate($templateName, $viewData, $cacheId = null);
}