<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

class TemplateNameResolver implements TemplateNameResolverInterface
{
    /**
     * @var string
     */
    private $templateExtension;

    public function __construct(string $templateExtension)
    {
        $this->templateExtension = $templateExtension;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function resolve(string $name): string
    {
        if ($name !== '') {
            $name = $name . '.' . $this->templateExtension;
        }
        return $name;
    }
}
