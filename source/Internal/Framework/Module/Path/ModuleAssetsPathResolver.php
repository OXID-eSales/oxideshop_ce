<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Path;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Webmozart\PathUtil\Path;

class ModuleAssetsPathResolver implements ModuleAssetsPathResolverInterface
{
    /**
     * @var BasicContextInterface
     */
    private $context;

    public function __construct(BasicContextInterface $context)
    {
        $this->context = $context;
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
