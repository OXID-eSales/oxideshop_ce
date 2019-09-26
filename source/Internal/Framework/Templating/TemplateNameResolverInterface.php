<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

/**
 * Interface TemplateNameResolverInterface
 * @internal
 */
interface TemplateNameResolverInterface
{
    /**
     * @param string $name
     *
     * @return string
     */
    public function resolve(string $name): string;
}
