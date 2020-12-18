<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Path;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModuleAssetsPathResolver;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class ModuleAssetsPathResolverTest extends TestCase
{
    use ProphecyTrait;

    public function testGetAssetsPath(): void
    {
        $context = $this->prophesize(BasicContextInterface::class);
        $context->getOutPath()->willReturn('outDirectory');

        $pathResolver = new ModuleAssetsPathResolver($context->reveal());

        $this->assertEquals(
            'outDirectory/modules/moduleId',
            $pathResolver->getAssetsPath('moduleId')
        );
    }
}
