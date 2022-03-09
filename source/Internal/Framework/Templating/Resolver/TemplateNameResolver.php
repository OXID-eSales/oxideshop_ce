<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;

/** @deprecated class will be changed to TemplateFileResolver in v7.0 */
class TemplateNameResolver implements TemplateNameResolverInterface
{
    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * TemplateNameResolver constructor.
     *
     * @param TemplateEngineInterface       $templateEngine
     */
    public function __construct(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function resolve(string $name): string
    {
        if ($name !== '') {
            $name = $name . '.' . $this->templateEngine->getDefaultFileExtension();
        }
        return $name;
    }
}
