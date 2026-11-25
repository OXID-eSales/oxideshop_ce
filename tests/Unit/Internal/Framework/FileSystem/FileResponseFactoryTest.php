<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\FileSystem;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileResponseFactory;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FileResponseFactoryTest extends TestCase
{
    private string $file;

    protected function setUp(): void
    {
        $this->file = (string) tempnam(sys_get_temp_dir(), 'frf');
        file_put_contents($this->file, 'file-body');
    }

    protected function tearDown(): void
    {
        if (is_file($this->file)) {
            unlink($this->file);
        }
    }

    #[DataProvider('factoryMethodDataProvider')]
    public function testResponseIsNotCacheable(string $factoryMethod): void
    {
        $response = $this->createResponse($factoryMethod);

        $this->assertSame('no-store, private', $response->headers->get('Cache-Control'));
    }

    #[DataProvider('factoryMethodDataProvider')]
    public function testResponseIsAttachment(string $factoryMethod): void
    {
        $response = $this->createResponse($factoryMethod);

        $this->assertSame('attachment; filename=export.csv', $response->headers->get('Content-Disposition'));
    }

    public static function factoryMethodDataProvider(): array
    {
        return [
            'file' => ['createFromFile'],
            'content' => ['createFromContent'],
            'streamed' => ['createStreamed'],
        ];
    }

    public function testCreateFromContentSetsContentLength(): void
    {
        $response = (new FileResponseFactory())->createFromContent('some-content', 'text/html', 'report.html');

        $this->assertSame('12', $response->headers->get('Content-Length'));
    }

    public function testNonAsciiDownloadFilenameGetsAsciiFallback(): void
    {
        $response = (new FileResponseFactory())->createFromContent('x', 'application/pdf', 'Rechnung März.pdf');

        $this->assertSame(
            "attachment; filename=\"Rechnung M__rz.pdf\"; filename*=utf-8''Rechnung%20M%C3%A4rz.pdf",
            $response->headers->get('Content-Disposition')
        );
    }

    public function testDownloadFilenameDirectoryPartsAreStripped(): void
    {
        $response = (new FileResponseFactory())->createFromContent('x', 'application/pdf', 'path/to\file.pdf');

        $this->assertSame('attachment; filename=file.pdf', $response->headers->get('Content-Disposition'));
    }

    private function createResponse(string $factoryMethod): Response
    {
        $factory = new FileResponseFactory();

        return match ($factoryMethod) {
            'createFromFile' => $factory->createFromFile($this->file, 'text/csv', 'export.csv'),
            'createFromContent' => $factory->createFromContent('file-body', 'text/csv', 'export.csv'),
            'createStreamed' => $factory->createStreamed(static fn() => null, 'text/csv', 'export.csv'),
        };
    }
}
