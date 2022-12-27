<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Path;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

class ModuleAssetsPathResolver implements ModuleAssetsPathResolverInterface
{
    public function __construct(private BasicContextInterface $context)
    {
    }

    public function getAssetsPath(string $moduleId): string
    {
        return Path::join(
            $this->context->getOutPath(),
            'modules',
            $moduleId
        );
    }
}
