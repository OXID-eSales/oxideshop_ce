<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\Http;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Http\LegacyRoutingListener;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class LegacyRoutingListenerTest extends IntegrationTestCase
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

    public function testDecodesSeoUrlForTheLegacyController(): void
    {
        $request = $this->legacyRequest($this->givenCategorySeoUrlPath(), '/oxseo.php');

        $this->handle($request);

        $this->assertSame('alist', $request->query->get('cl'));
        $this->assertSame('seo-listener-category', $request->query->get('cnid'));
    }

    public function testRejectsUnknownSeoPathWithNotFound(): void
    {
        $request = $this->legacyRequest('/unknown-seo-path/', '/oxseo.php');

        $this->expectException(NotFoundHttpException::class);

        $this->handle($request);
    }

    public function testRejectsUnknownPathOfOtherEntryPointsWithoutSeoDecoding(): void
    {
        $request = $this->legacyRequest('/index.php/unknown', '/index.php');

        $this->expectException(NotFoundHttpException::class);

        $this->handle($request);
    }

    public function testLeavesRequestsRoutedToAnotherControllerUndecoded(): void
    {
        $request = $this->legacyRequest($this->givenCategorySeoUrlPath(), '/oxseo.php');
        $request->attributes->set('_controller', 'routed-controller');

        $this->handle($request);

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

    private function legacyRequest(string $path, string $scriptName): Request
    {
        $controller = static fn(): Response => new Response();
        $request = Request::create($path, server: ['SCRIPT_NAME' => $scriptName]);
        $request->attributes->set('_fallback_controller', $controller);
        $request->attributes->set('_controller', $controller);

        return $request;
    }

    private function handle(Request $request): void
    {
        new LegacyRoutingListener()->onKernelRequest(
            new RequestEvent($this->createStub(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST)
        );
    }
}
