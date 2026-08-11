<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Media\Service\GeneratedImagePathProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class GeneratedImagePathProviderTest extends TestCase
{
    #[DataProvider('generatedImageRequestUriProvider')]
    public function testGetGeneratedImagePath(string $requestUri, string $expectedPath): void
    {
        $provider = $this->createProvider($requestUri);

        $this->assertSame($expectedPath, $provider->getGeneratedImagePath());
    }

    public static function generatedImageRequestUriProvider(): array
    {
        return [
            'generated image path' => [
                '/out/pictures/generated/media/400_400_75/image.jpg',
                'out/pictures/generated/media/400_400_75/image.jpg',
            ],
            'shop installed in subdirectory' => [
                '/shop/out/pictures/generated/media/400_400_75/image.jpg',
                'out/pictures/generated/media/400_400_75/image.jpg',
            ],
            'encoded filename and query string' => [
                '/out/pictures/generated/media/400_400_75/image%20one.jpg?version=1',
                'out/pictures/generated/media/400_400_75/image one.jpg',
            ],
            'canonical path' => [
                '/out//pictures/generated/media/../400_400_75/image.jpg',
                'out/pictures/generated/400_400_75/image.jpg',
            ],
        ];
    }

    #[DataProvider('unrelatedRequestUriProvider')]
    public function testGetGeneratedImagePathWithUnrelatedRequestReturnsEmptyPath(string $requestUri): void
    {
        $provider = $this->createProvider($requestUri);

        $this->assertSame('', $provider->getGeneratedImagePath());
    }

    public static function unrelatedRequestUriProvider(): array
    {
        return [
            'empty URI' => [''],
            'unrelated path' => ['/index.php'],
            'generated path in query string' => [
                '/getimg.php?out/pictures/generated/../../../../composer.json',
            ],
            'path traversal' => [
                '/out/pictures/generated/../../../../composer.json',
            ],
            'encoded path traversal' => [
                '/out/pictures/generated/%2e%2e/%2e%2e/%2e%2e/index.php',
            ],
            'backslash path traversal' => [
                '/out/pictures/generated\\..\\..\\index.php',
            ],
            'directory prefix only' => [
                '/out/pictures/generated-backup/image.jpg',
            ],
        ];
    }

    private function createProvider(string $requestUri): GeneratedImagePathProvider
    {
        return new GeneratedImagePathProvider(
            new Request(server: ['REQUEST_URI' => $requestUri])
        );
    }
}
