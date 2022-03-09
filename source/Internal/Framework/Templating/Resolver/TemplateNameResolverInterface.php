<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

/** @deprecated interface will be changed to TemplateFileResolverInterface in v7.0 */
interface TemplateNameResolverInterface
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function resolve(string $name): string;
}
