<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

interface TemplateEngineInterface
{
    /**
     * @param mixed $value
     */
    public function addGlobal(string $name, $value);

    /**
     * Returns assigned globals.
     */
    public function getGlobals(): array;

    /**
     * Returns the template file extension.
     */
    public function getDefaultFileExtension(): string;

    /**
     * Renders a template.
     *
     * @param string $name    A template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     */
    public function render(string $name, array $context = []): string;

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment   The template fragment to render
     * @param string $fragmentId The Id of the fragment
     * @param array  $context    An array of parameters to pass to the template
     */
    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string;

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     */
    public function exists(string $name): bool;
}
