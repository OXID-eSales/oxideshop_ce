<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use PHPUnit\Framework\TestCase;

final class ThemeMetaDataTest extends TestCase
{
    public function testSetIdRejectsEmptyString(): void
    {
        $this->expectException(InvalidThemeMetaDataException::class);

        (new ThemeMetaData())->setId('');
    }

    public function testSetIdAcceptsNonEmptyString(): void
    {
        $metaData = (new ThemeMetaData())->setId('my-theme');

        $this->assertSame('my-theme', $metaData->getId());
    }
}
