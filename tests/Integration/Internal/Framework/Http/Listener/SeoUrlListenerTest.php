<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Listener\SeoUrlListener;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class SeoUrlListenerTest extends IntegrationTestCase
{
    private array $query;

    public function setUp(): void
    {
        parent::setUp();

        $this->query = $_GET;
        $_GET = [];
    }

    public function tearDown(): void
    {
        $_GET = $this->query;

        parent::tearDown();
    }

    public function testAddsDecodedSeoUrlParametersToTheRequest(): void
    {
        $request = $this->request($this->givenCategorySeoUrlPath(), '/oxseo.php');

        new SeoUrlListener()->onKernelRequest($this->event($request, HttpKernelInterface::MAIN_REQUEST));

        $this->assertSame('alist', $request->query->get('cl'));
        $this->assertSame('seo-listener-category', $request->query->get('cnid'));
    }

    public function testLeavesRequestsOfOtherEntryPointsUntouched(): void
    {
        $request = $this->request($this->givenCategorySeoUrlPath(), '/index.php');

        new SeoUrlListener()->onKernelRequest($this->event($request, HttpKernelInterface::MAIN_REQUEST));

        $this->assertSame([], $request->query->all());
    }

    public function testLeavesSubRequestsUntouched(): void
    {
        $request = $this->request($this->givenCategorySeoUrlPath(), '/oxseo.php');

        new SeoUrlListener()->onKernelRequest($this->event($request, HttpKernelInterface::SUB_REQUEST));

        $this->assertSame([], $request->query->all());
    }

    private function givenCategorySeoUrlPath(): string
    {
        $category = oxNew(Category::class);
        $category->setId('seo-listener-category');
        $category->assign(['oxtitle' => 'SeoListenerCategory', 'oxactive' => 1, 'oxparentid' => 'oxrootid']);
        $category->save();

        $storedCategory = oxNew(Category::class);
        $storedCategory->load('seo-listener-category');

        return parse_url(oxNew(SeoEncoderCategory::class)->getCategoryUrl($storedCategory), PHP_URL_PATH);
    }

    private function request(string $path, string $scriptName): Request
    {
        return Request::create($path, server: ['SCRIPT_NAME' => $scriptName]);
    }

    private function event(Request $request, int $requestType): RequestEvent
    {
        return new RequestEvent($this->createStub(HttpKernelInterface::class), $request, $requestType);
    }
}
