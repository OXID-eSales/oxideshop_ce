<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Application\Model\DiagnosticsOutput;
use PHPUnit\Framework\TestCase;

/**
 * @runInSeparateProcess
 */
final class DiagnosticsOutputTest extends TestCase
{
    private string $key = "diagnostic_tool_result";
    private string $sOutputFileName = "diagnostic_tool_result.html";

    /**
    * @runInSeparateProcess
    */
    public function testDownloadResultFileWillSetCorrectContentLengthHeader(): void
    {
        $content = 'some-content-123';
        $contentLength = strlen($content);

        $utils = new UtilsSpy();
        Registry::set(Utils::class, $utils);
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

    /**
     * @dataProvider headerValuesProvider
     * @runInSeparateProcess
     */
    public function testItShouldSetCorrectHeaderValue(string $headerValue): void
    {
        $utils = new UtilsSpy();
        Registry::set(Utils::class, $utils);

        $diagnostics = new DiagnosticsOutput();
        ob_start();
        $diagnostics->downloadResultFile($this->key);
        ob_end_clean();

        $this->assertArrayHasKey(
            $headerValue,
            $utils->getHeaders()
        );
    }

    public function headerValuesProvider(): array
    {
        return [
            ['Pragma: public'],
            ['Expires: 0'],
            ['Cache-Control: must-revalidate, post-check=0, pre-check=0, private'],
            ['Content-Type:text/html;charset=utf-8'],
            ['Content-Disposition: attachment;filename=' . $this->sOutputFileName],
        ];
    }

    /**
     * @runInSeparateProcess
     */
    public function testDownloadResultFilePrintsOutput(): void
    {
        $oUtils = Registry::getUtils();
        $content = $oUtils->fromFileCache($this->key);

        $this->expectOutputString($content);

        $diagnostics = new DiagnosticsOutput();
        $diagnostics->downloadResultFile($this->key);
    }
}
