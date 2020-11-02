<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader;

/**
 * Interface TemplateLoaderInterface.
 */
interface TemplateLoaderInterface
{
    /**
     * Check a template exists.
     *
     * @param string $name The name of the template
     */
    public function exists($name): bool;

    /**
     * Returns the content of the given template.
     *
     * @param string $name The name of the template
     */
    public function getContext($name): string;

    /**
     * Returns the path to the template.
     *
     * @param string $name A template name
     */
    public function getPath($name): string;
}
