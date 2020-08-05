<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidTemplateNameException;

interface TemplateFileResolverInterface
{
    /**
     * @param string $templateName
     * @return string
     * @throws InvalidTemplateNameException
     */
    public function getFilename(string $templateName): string;
}
