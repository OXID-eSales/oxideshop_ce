<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

class TemplateRenderer implements TemplateRendererInterface
{
    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    public function __construct(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param string $template The template name
     * @param array  $context  An array of parameters to pass to the template
     */
    public function renderTemplate(string $template, array $context = []): string
    {
        return $this->getTemplateEngine()->render($template, $context);
    }

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment   The template fragment to render
     * @param string $fragmentId The id of the fragment
     * @param array  $context    An array of parameters to pass to the template
     */
    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string
    {
        return $this->getTemplateEngine()->renderFragment($fragment, $fragmentId, $context);
    }

    /**
     * Return fallback engine.
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
}
