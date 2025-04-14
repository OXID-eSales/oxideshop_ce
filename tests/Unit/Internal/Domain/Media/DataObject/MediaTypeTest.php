<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\DataObject;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaType;
use PHPUnit\Framework\TestCase;

final class MediaTypeTest extends TestCase
{
    public function testValidMimeType(): void
    {
        $mediaType = new MediaType('image/png');

        $this->assertEquals('image/png', (string)$mediaType);
    }

    public function testValidMimeTypeWithDotAndPlus(): void
    {
        $mediaType = new MediaType('application/vnd.ms-excel.sheet.macroEnabled.12');
        $this->assertEquals('application/vnd.ms-excel.sheet.macroEnabled.12', (string)$mediaType);

        $mediaType = new MediaType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string)$mediaType);

        $mediaType = new MediaType('application/json+zip');
        $this->assertEquals('application/json+zip', (string)$mediaType);
    }

    public function testInvalidMimeTypeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MediaType('invalidtype');
    }

    public function testEmptyMimeTypeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MediaType('');
    }

    public function testMimeTypeWithSpaceThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new MediaType('image/ png');
    }
}
