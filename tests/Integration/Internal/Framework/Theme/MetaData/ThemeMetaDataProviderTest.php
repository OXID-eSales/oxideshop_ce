<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\MetaData;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataProviderInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ThemeMetaDataProviderTest extends TestCase
{
    use ContainerTrait;

    private string $themePath = __DIR__ . '/../Install/Fixtures/testTheme';

    public function testGetReturnsCorrectId(): void
    {
        $metadata = $this->get(ThemeMetaDataProviderInterface::class)->get($this->themePath);

        $this->assertSame('testTheme', $metadata->getId());
    }

    public function testGetReturnsCorrectScalarFields(): void
    {
        $metadata = $this->get(ThemeMetaDataProviderInterface::class)->get($this->themePath);

        $this->assertSame('1.0.0', $metadata->getVersion());
        $this->assertSame('Test Theme', $metadata->getTitle());
        $this->assertSame('Theme used in integration tests', $metadata->getDescription());
        $this->assertSame('preview.png', $metadata->getThumbnail());
        $this->assertSame('OXID eSales AG', $metadata->getAuthor());
    }

    public function testThrowsWhenMetadataFileNotReadable(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->get(ThemeMetaDataProviderInterface::class)->get('/non/existent/path');
    }
}
