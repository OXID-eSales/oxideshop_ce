<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\Http;

use OxidEsales\Eshop\Application\Model\Category;
use OxidEsales\Eshop\Application\Model\SeoEncoderCategory;
use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Http\LegacyRoutingListener;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class LegacyRoutingListenerTest extends IntegrationTestCase
{
    use ContainerTrait;

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

    public function testLetsQueryParametersOverrideDecodedSeoParameters(): void
    {
        $request = $this->legacyRequest($this->givenCategorySeoUrlPath() . '?cl=search', '/oxseo.php');
        $_GET = $request->query->all();

        $this->handle($request);

        $this->assertSame('search', $request->query->get('cl'));
        $this->assertSame('seo-listener-category', $request->query->get('cnid'));
    }

    public function testRejectsUnknownSeoPath(): void
    {
        $request = $this->legacyRequest('/unknown-seo-path/', '/oxseo.php');

        $this->expectException(RoutingException::class);

        $this->handle($request);
    }

    public function testRejectsUnknownSeoPathWithControllerKey(): void
    {
        $request = $this->legacyRequest('/unknown-seo-path/?cl=start', '/oxseo.php');
        $_GET = $request->query->all();

        $this->expectException(RoutingException::class);

        $this->handle($request);
    }

    public function testKernelAnswersUnknownSeoPathWithNotFound(): void
    {
        $this->setParameter('oxid_esales.debug_mode', false);
        $request = Request::create(
            '/unknown-seo-path/?cl=start',
            server: ['SCRIPT_NAME' => '/oxseo.php', 'HTTP_ACCEPT' => 'application/json']
        );
        $request->attributes->set('_fallback_controller', static fn(): Response => new Response('legacy-page'));
        $_GET = $request->query->all();

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
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
        $this->get(LegacyRoutingListener::class)->onKernelRequest(
            new RequestEvent($this->createStub(HttpKernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST)
        );
    }
}
