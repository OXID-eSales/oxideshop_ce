<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

/**
 * @deprecated will be removed in next major
 */
interface TemplateFileResolverBridgeInterface
{
    public function getFilename(string $templateName): string;
}
