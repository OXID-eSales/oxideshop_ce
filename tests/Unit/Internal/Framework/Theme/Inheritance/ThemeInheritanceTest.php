<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Inheritance;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\ParentThemeNotFoundException;
use PHPUnit\Framework\TestCase;

final class ThemeInheritanceTest extends TestCase
{
    public function testHasParentThemeReturnsFalseForThemeWithoutParent(): void
    {
        $inheritance = new ThemeInheritance('child', null);

        $this->assertFalse($inheritance->hasParentTheme());
    }

    public function testHasParentThemeReturnsTrueForThemeWithParent(): void
    {
        $inheritance = new ThemeInheritance('child', 'parent');

        $this->assertTrue($inheritance->hasParentTheme());
    }

    public function testGetParentThemeIdReturnsParentId(): void
    {
        $inheritance = new ThemeInheritance('child', 'parent');

        $this->assertSame('parent', $inheritance->getParentThemeId());
    }

    public function testGetParentThemeIdThrowsWhenThemeHasNoParent(): void
    {
        $inheritance = new ThemeInheritance('child', null);

        $this->expectException(ParentThemeNotFoundException::class);

        $inheritance->getParentThemeId();
    }
}
