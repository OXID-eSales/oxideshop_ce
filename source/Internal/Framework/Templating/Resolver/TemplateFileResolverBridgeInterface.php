<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

interface TemplateFileResolverBridgeInterface
{
    /**
     * @param string $templateName
     * @return string
     */
    public function getFilename(string $templateName): string;
}
