<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Loader;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\TemplateFileNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\FileLocatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver\TemplateFileResolverInterface;

class TemplateLoader implements TemplateLoaderInterface
{
    public function __construct(
        private FileLocatorInterface $fileLocator,
        private TemplateFileResolverInterface $templateFileResolver
    ) {
    }

    /**
     * Check a template exists.
     *
     * @param string $name The name of the template
     *
     * @return bool
     */
    public function exists($name): bool
    {
        try {
            $this->findTemplate($name);
        } catch (TemplateFileNotFoundException) {
            return false;
        }
        return true;
    }

    /**
     * Returns the content of the given template.
     *
     * @param string $name The name of the template
     *
     * @return string
     *
     * @throws TemplateFileNotFoundException
     */
    public function getContext($name): string
    {
        $path = $this->findTemplate($name);

        return file_get_contents($path);
    }

    /**
     * @param string $name A template name
     *
     * @return string
     *
     * @throws TemplateFileNotFoundException
     */
    private function findTemplate($name): string
    {
        $filename = $this->templateFileResolver->getFilename($name);
        $file = $this->fileLocator->locate($filename);

        if (false === $file || null === $file || '' === $file) {
            throw new TemplateFileNotFoundException(sprintf('Template "%s" not found', $name));
        }
        return $file;
    }
}
