<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

final class HttpKernelServiceTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testKernelServiceIsTerminable(): void
    {
        $this->assertInstanceOf(TerminableInterface::class, $this->get(HttpKernelInterface::class));
    }

    public function testRunsTheControllerFromRequestAttributes(): void
    {
        $request = $this->requestWithController(static fn(): Response => new Response('from-controller'));

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame('from-controller', $response->getContent());
    }

    public function testDeliversResponseSignaledFromTheController(): void
    {
        $expected = new Response('signaled');
        $request = $this->requestWithController(static function () use ($expected): Response {
            throw new ResponseReady($expected);
        });

        $response = $this->get(HttpKernelInterface::class)->handle($request);

        $this->assertSame($expected, $response);
    }

    private function requestWithController(callable $controller): Request
    {
        $request = new Request();
        $request->attributes->set('_controller', $controller);

        return $request;
    }
}
