<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidTemplateNameException;

/**
 * @deprecated will be removed in next major
 */
interface TemplateFileResolverInterface
{
    /**
     * @throws InvalidTemplateNameException
     */
    public function getFilename(string $templateName): string;
}
