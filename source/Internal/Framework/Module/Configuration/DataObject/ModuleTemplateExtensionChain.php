<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject;

use RecursiveArrayIterator;

class ModuleTemplateExtensionChain extends RecursiveArrayIterator
{
    public const NAME = 'templateExtensions';

    public function getTemplateLoadingPriority(string $templateName): ModuleIdChain
    {
        return new ModuleIdChain(
            $this->offsetGet($this->normalizeTemplateName($templateName)) ?? []
        );
    }

    /**
     * SymfonyConfig replaces hyphens with underscores for ArrayNode keys
     * @see \Symfony\Component\Config\Definition\ArrayNode::preNormalize
     */
    private function normalizeTemplateName(string $templateName): string
    {
        return str_contains($templateName, '-') && !str_contains($templateName, '_')
            ? str_replace('-', '_', $templateName)
            : $templateName;
    }
}
