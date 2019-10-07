<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

interface TemplateRendererInterface
{
    /**
     * @param string $template The template name
     * @param array  $context  An array of parameters to pass to the template
     *
     * @return string
     */
    public function renderTemplate(string $template, array $context = []): string;

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment The template fragment to render
     * @param string $fragmentId The id of the fragment
     * @param array  $context    An array of parameters to pass to the template
     *
     * @return string
     */
    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string;

    /**
     * @return TemplateEngineInterface
     */
    public function getTemplateEngine(): TemplateEngineInterface;

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists(string $name): bool;
}
