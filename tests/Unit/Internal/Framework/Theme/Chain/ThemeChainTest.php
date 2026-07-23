<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\ThemeChain;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ParentThemeNotFoundException;
use PHPUnit\Framework\TestCase;

final class ThemeChainTest extends TestCase
{
    public function testHasParentThemeReturnsFalseForThemeWithoutParent(): void
    {
        $chain = new ThemeChain(['child']);

        $this->assertFalse($chain->hasParentTheme());
    }

    public function testHasParentThemeReturnsTrueForThemeWithParent(): void
    {
        $chain = new ThemeChain(['child', 'parent']);

        $this->assertTrue($chain->hasParentTheme());
    }

    public function testGetParentThemeIdReturnsParentId(): void
    {
        $chain = new ThemeChain(['child', 'parent']);

        $this->assertSame('parent', $chain->getParentThemeId());
    }

    public function testGetParentThemeIdThrowsWhenThemeHasNoParent(): void
    {
        $chain = new ThemeChain(['child']);

        $this->expectException(ParentThemeNotFoundException::class);

        $chain->getParentThemeId();
    }
}