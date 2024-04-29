<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\TestsToRefactor;

use OxidEsales\Eshop\Application\Controller\Admin\ShopLicense;
use OxidEsales\Eshop\Core\Curl;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Core\ShopVersion;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class VersionCheckerResponseTest extends IntegrationTestCase
{
    public function testRenderWillWorkWithEmptyResponse(): void
    {
        $versionCheckerResponse = '';
        $versionInfo = $this->getRenderedVersionInfo($versionCheckerResponse);

        $this->assertEmpty($versionInfo);
    }

    public function testRenderWillStripTags(): void
    {
        $versionCheckerResponse = '<b>abc <br><script>xyz</script></b>';
        $versionInfo = $this->getRenderedVersionInfo($versionCheckerResponse);

        $this->assertSame('<b>abc <br>xyz</b>', $versionInfo);
    }

    public function testRenderWillUnifyBreakTags(): void
    {
        $versionCheckerResponse = '<br />abc<br><br /><br/>';
        $versionInfo = $this->getRenderedVersionInfo($versionCheckerResponse);

        $this->assertSame('<br>abc<br><br><br>', $versionInfo);
    }

    public function testRenderWillWorkWithOnlySingleVersionStringPresentInResponse(): void
    {
        $versionCheckerResponse = 'v1.2.3';
        $versionInfo = $this->getRenderedVersionInfo($versionCheckerResponse);

        $this->assertSame($versionCheckerResponse, $versionInfo);
    }

    public function testRenderWithCurrentVersionSameAsNewest(): void
    {
        $currentVersion = ShopVersion::getVersion();
        $versionCheckerResponse = "$currentVersion and $currentVersion";
        $versionInfo = $this->getRenderedVersionInfo($versionCheckerResponse);

        $this->assertSame($versionCheckerResponse, $versionInfo);
    }

    public function testRenderWithCurrentVersionSmallerThanNewestWillWrapUpdateTextWithLink(): void
    {
        $currentVersion = ShopVersion::getVersion();
        $versionCheckerResponse = "v$currentVersion and v999.999.999<br>some update it! text<br>some last text row";
        $versionInfo = $this->getRenderedVersionInfo($versionCheckerResponse);

        $documentationLink = Registry::getLang()->translateString('VERSION_UPDATE_LINK');

        $this->assertStringStartsWith("v$currentVersion and v999.999.999<br><a", $versionInfo);
        $this->assertStringContainsString($documentationLink, $versionInfo);
        $this->assertStringEndsWith('some update it! text</a><br>some last text row', $versionInfo);
    }

    public function testRenderWithCurrentVersionGreaterThanNewest(): void
    {
        $currentVersion = ShopVersion::getVersion();
        $versionCheckerResponse = "v$currentVersion and v0.0.1<br>some update-it! text<br>some text";
        $versionInfo = $this->getRenderedVersionInfo($versionCheckerResponse);

        $this->assertEquals($versionCheckerResponse, $versionInfo);
    }

    private function getRenderedVersionInfo(string $mockedResponse): string
    {
        $curlMock = $this->createMock(Curl::class);
        $curlMock->method('execute')->willReturn($mockedResponse);
        UtilsObject::setClassInstance(Curl::class, $curlMock);

        $controller = oxNew(ShopLicense::class);
        $controller->render();

        return $controller->getViewData()['aCurVersionInfo'];
    }
}
