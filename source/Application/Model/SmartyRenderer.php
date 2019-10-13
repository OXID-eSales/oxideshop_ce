<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use Psr\Container\ContainerInterface;

/**
 * @deprecated since v6.4 (2019-10-10); Use TemplateRendererBridgeInterface
 *
 * Smarty renderer class
 * Renders smarty template with given parameters and returns rendered body.
 *
 */
class SmartyRenderer
{
    /**
     * Template renderer
     *
     * @param string $sTemplateName Template name.
     * @param array  $aViewData     Array of view data (optional).
     *
     * @return string
     */
    public function renderTemplate($sTemplateName, $aViewData = [])
    {
        $renderer = $this->getContainer()
            ->get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
        return $renderer->renderTemplate($sTemplateName, $aViewData);
    }

    /**
     * @internal
     *
     * @return ContainerInterface
     */
    private function getContainer()
    {
        return \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->getContainer();
    }
}
