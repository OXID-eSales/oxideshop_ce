<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class TemplateRenderer implements TemplateRendererInterface
{
    public function __construct(
        private TemplateEngineInterface $templateEngine,
        private ContextInterface $context
    ) {
    }

    /**
     * @param string $template The template name
     * @param array  $context  An array of parameters to pass to the template
     *
     * @return string
     */
    public function renderTemplate(string $template, array $context = []): string
    {
        return $this->getTemplateEngine()->render($template, $context);
    }

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment The template fragment to render
     * @param string $fragmentId The id of the fragment
     * @param array  $context    An array of parameters to pass to the template
     *
     * @return string
     */
    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string
    {
        if ($this->doNotRenderForDemoShop()) {
            return $fragment;
        }
        return $this->getTemplateEngine()->renderFragment($fragment, $fragmentId, $context);
    }

    /**
     * Return fallback engine.
     *
     * @return TemplateEngineInterface
     */
    public function getTemplateEngine(): TemplateEngineInterface
    {
        return $this->templateEngine;
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists(string $name): bool
    {
        return $this->getTemplateEngine()->exists($name);
    }

    private function doNotRenderForDemoShop(): bool
    {
        return $this->context->isShopInDemoMode();
    }
}
