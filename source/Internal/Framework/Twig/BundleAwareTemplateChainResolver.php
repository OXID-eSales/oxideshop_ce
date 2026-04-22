<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Twig;

use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;

class BundleAwareTemplateChainResolver implements TemplateChainResolverInterface
{
    public function __construct(private TemplateChainResolverInterface $inner)
    {
    }

    public function hasParent(string $templateName): bool
    {
        try {
            return $this->inner->hasParent($templateName);
        } catch (\Exception) {
            return false;
        }
    }

    public function getParent(string $templateName): string
    {
        return $this->inner->getParent($templateName);
    }

    public function getLastChild(string $templateName): string
    {
        try {
            return $this->inner->getLastChild($templateName);
        } catch (\Exception) {
            return $templateName;
        }
    }
}
