<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setting\Helper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Helper\ModuleIdPreparator;
use PHPUnit\Framework\TestCase;

final class ModuleIdPreparatorTest extends TestCase
{
    public function testPrepare(): void
    {
        $preparator = new ModuleIdPreparator();

        $this->assertSame('module:test', $preparator->prepare('test'));
    }
}
