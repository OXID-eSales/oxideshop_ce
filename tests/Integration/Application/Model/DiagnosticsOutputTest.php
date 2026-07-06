<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\DiagnosticsOutput;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;

final class DiagnosticsOutputTest extends TestCase
{
    private string $key = 'diagnostic_tool_result';

    public function testDownloadResultFileBuildsResponseWithExpectedHeaders(): void
    {
        $content = 'some-content-123';
        Registry::getUtils()->toFileCache($this->key, $content);

        $response = $this->runDownload($this->key);

        $this->assertSame((string)strlen($content), $response->headers->get('Content-Length'));
        $this->assertStringContainsString('text/html', (string)$response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', (string)$response->headers->get('Content-Disposition'));
    }

    public function testDownloadResultFileCarriesCachedContent(): void
    {
        $content = 'some-content-123';
        Registry::getUtils()->toFileCache($this->key, $content);

        $response = $this->runDownload($this->key);

        $this->assertSame($content, $response->getContent());
    }

    public function testDownloadResultFileWithEmptyCacheReturnsNotFound(): void
    {
        $response = $this->runDownload('missing_diagnostics_key_xyz');

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    private function runDownload(string $key): Response
    {
        return (new DiagnosticsOutput())->getResultFileResponse($key);
    }
}
