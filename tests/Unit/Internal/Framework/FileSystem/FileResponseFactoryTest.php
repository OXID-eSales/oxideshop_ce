<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\FileSystem;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileResponseFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

    public function testFromCallbackBuildsStreamedResponse(): void
    {
        $response = (new FileResponseFactory())->fromCallback(
            static fn() => print('x'),
            'text/csv; charset=utf-8',
            'export.csv'
        );

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertSame('text/csv; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('export.csv', (string)$response->headers->get('Content-Disposition'));
        $this->assertNoCacheHeaders($response);
    }

    public function testNotFoundReturns404(): void
    {
        $this->assertSame(Response::HTTP_NOT_FOUND, (new FileResponseFactory())->notFound()->getStatusCode());
    }

    public function testFromFileCarriesNoCacheHeaders(): void
    {
        $response = (new FileResponseFactory())->fromFile($this->file, 'text/csv; charset=utf-8', 'export.csv');

        $this->assertInstanceOf(BinaryFileResponse::class, $response);
        $this->assertSame('text/csv; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', (string)$response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('export.csv', (string)$response->headers->get('Content-Disposition'));
        $this->assertNoCacheHeaders($response);
    }

    public function testFromContentCarriesBodyLengthAndNoCacheHeaders(): void
    {
        $response = (new FileResponseFactory())->fromContent('some-content', 'text/html; charset=utf-8', 'report.html');

        $this->assertSame('some-content', $response->getContent());
        $this->assertSame('text/html; charset=utf-8', $response->headers->get('Content-Type'));
        $this->assertSame((string) strlen('some-content'), $response->headers->get('Content-Length'));
        $this->assertStringContainsString('report.html', (string)$response->headers->get('Content-Disposition'));
        $this->assertNoCacheHeaders($response);
    }

    public function testNonAsciiDownloadFilenameGetsAsciiFallback(): void
    {
        $response = (new FileResponseFactory())->fromContent('x', 'application/pdf', 'Rechnung März.pdf');

        $disposition = (string) $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('filename=Rechnung Marz.pdf', str_replace('"', '', $disposition));
        $this->assertStringContainsString("filename*=utf-8''Rechnung%20M%C3%A4rz.pdf", $disposition);
    }

    public function testDownloadFilenameDirectoryPartsAreStripped(): void
    {
        $response = (new FileResponseFactory())->fromContent('x', 'application/pdf', 'path/to\file.pdf');

        $disposition = (string) $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('filename=file.pdf', str_replace('"', '', $disposition));
        $this->assertStringNotContainsString('path', $disposition);
    }

    private function assertNoCacheHeaders(Response $response): void
    {
        $this->assertSame('no-store, private', $response->headers->get('Cache-Control'));
    }
}
