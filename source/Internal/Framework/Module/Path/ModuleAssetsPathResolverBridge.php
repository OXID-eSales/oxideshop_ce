<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Path;

class ModuleAssetsPathResolverBridge implements ModuleAssetsPathResolverInterface
{
    /**
     * @var ModuleAssetsPathResolverInterface
     */
    private $moduleAssetsPathResolver;

    public function __construct(ModuleAssetsPathResolverInterface $moduleAssetsPathResolver)
    {
        $this->moduleAssetsPathResolver = $moduleAssetsPathResolver;
    }

    public function getAssetsPath(string $moduleId): string
    {
        return $this->moduleAssetsPathResolver->getAssetsPath($moduleId);
    }
}
