<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * A test bundle with prepend extension support.
 *
 * @internal
 */
class PrependableBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new PrependableExtension();
    }
}
