<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\TemplateFileNotFoundException;

/**
 * Class TemplateLoader
 * @package OxidEsales\EshopCommunity\Internal\Framework\Templating
 */
class TemplateLoader implements TemplateLoaderInterface
{
    /**
     * @var TemplateNameResolverInterface
     */
    private $templateNameResolver;

    /**
     * @var TemplateFileLocatorInterface
     */
    private $fileLocator;

    /**
     * TemplateLoader constructor.
     *
     * @param TemplateFileLocatorInterface  $fileLocator
     * @param TemplateNameResolverInterface $templateNameResolver
     */
    public function __construct(
        TemplateFileLocatorInterface $fileLocator,
        TemplateNameResolverInterface $templateNameResolver
    ) {
        $this->fileLocator = $fileLocator;
        $this->templateNameResolver = $templateNameResolver;
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
        } catch (TemplateFileNotFoundException $e) {
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
     * Returns the path to the template.
     *
     * @param string $name A template name
     *
     * @return string
     *
     * @throws TemplateFileNotFoundException
     */
    public function getPath($name): string
    {
        return $this->findTemplate($name);
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
        $templateName = $this->templateNameResolver->resolve($name);
        $file = $this->fileLocator->locate($templateName);

        if (false === $file || null === $file || '' === $file) {
            throw new TemplateFileNotFoundException(sprintf('Template "%s" not found', $name));
        }
        return $file;
    }
}
