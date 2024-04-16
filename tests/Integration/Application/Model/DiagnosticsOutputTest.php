<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Application\Model\DiagnosticsOutput;
use PHPUnit\Framework\TestCase;

final class DiagnosticsOutputTest extends TestCase
{
    private string $key = "diagnostic_tool_result";
    private static string $sOutputFileName = "diagnostic_tool_result.html";

    #[RunInSeparateProcess]
    public function testDownloadResultFileWillSetCorrectContentLengthHeader(): void
    {
        $content = 'some-content-123';
        $contentLength = strlen($content);

        $utils = $this->getUtils();
        $cacheFilePath = $utils->getCacheFilePath($this->key);
        file_put_contents(
            $cacheFilePath,
            serialize(['content' => $content])
        );
        $diagnostics = new DiagnosticsOutput();
        ob_start();
        $diagnostics->downloadResultFile($this->key);
        ob_end_clean();

        $this->assertArrayHasKey(
            "Content-Length: $contentLength",
            $utils->getHeaders()
        );
    }

    #[DataProvider('headerValuesProvider')]
    #[RunInSeparateProcess]
    public function testItShouldSetCorrectHeaderValue(string $headerValue): void
    {
        $utils = $this->getUtils();

        $diagnostics = new DiagnosticsOutput();
        ob_start();
        $diagnostics->downloadResultFile($this->key);
        ob_end_clean();

        $this->assertArrayHasKey(
            $headerValue,
            $utils->getHeaders()
        );
    }

    public static function headerValuesProvider(): array
    {
        return [
            ['Pragma: public'],
            ['Expires: 0'],
            ['Cache-Control: must-revalidate, post-check=0, pre-check=0, private'],
            ['Content-Type:text/html;charset=utf-8'],
            ['Content-Disposition: attachment;filename=' . self::$sOutputFileName],
        ];
    }

    #[RunInSeparateProcess]
    public function testDownloadResultFilePrintsOutput(): void
    {
        $utils = $this->getUtils();
        $content = 'some-content-123';
        file_put_contents($utils->getCacheFilePath($this->key), serialize(['content' => $content]));

        $this->expectOutputString($content);

        $diagnostics = new DiagnosticsOutput();
        $diagnostics->downloadResultFile($this->key);
    }

    private function getUtils(): UtilsSpy
    {
        $utils = new UtilsSpy();
        Registry::set(Utils::class, $utils);

        return $utils;
    }
}
