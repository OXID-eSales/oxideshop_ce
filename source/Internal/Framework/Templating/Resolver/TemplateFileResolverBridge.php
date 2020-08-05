<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidTemplateNameException;

class TemplateFileResolverBridge implements TemplateFileResolverBridgeInterface
{
    private TemplateFileResolverInterface $resolver;

    public function __construct(
        TemplateFileResolverInterface $resolver
    ) {

        $this->resolver = $resolver;
    }
    /** @inheritDoc */
    public function getFilename(string $templateName): string
    {
        try {
            return $this->resolver->getFilename($templateName);
        } catch (InvalidTemplateNameException $e) {
            return '';
        }
    }
}
