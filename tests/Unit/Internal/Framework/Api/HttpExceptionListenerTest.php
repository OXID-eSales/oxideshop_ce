<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Api;

use OxidEsales\EshopCommunity\Internal\Framework\Api\HttpExceptionListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;

class HttpExceptionListenerTest extends TestCase
{
    public function testNotFoundHttpExceptionReturns404(): void
    {
        $listener = new HttpExceptionListener();
        $event = $this->createExceptionEvent(new NotFoundHttpException('Resource not found'));

        $listener->onKernelException($event);

        $this->assertSame(404, $event->getResponse()->getStatusCode());
    }

    #[DataProvider('productionModeValuesProvider')]
    public function testReturnsGenericMessageInProductionMode(string $envValue): void
    {
        putenv("OXID_DEBUG_MODE=$envValue");

        $listener = new HttpExceptionListener();
        $event = $this->createExceptionEvent(new NotFoundHttpException('Sensitive internal path info'));

        $listener->onKernelException($event);

        $response = json_decode($event->getResponse()->getContent(), true);
        $this->assertSame('Not Found', $response['error']);
    }

    public static function productionModeValuesProvider(): array
    {
        return [
            'empty string' => [''],
            'string 0' => ['0'],
            'string false' => ['false'],
            'string off' => ['off'],
            'string no' => ['no'],
            'random string' => ['random'],
            'numeric 2' => ['2'],
            'whitespace' => [' '],
        ];
    }

    #[DataProvider('debugModeValuesProvider')]
    public function testReturnsActualMessageInDebugMode(string $envValue): void
    {
        putenv("OXID_DEBUG_MODE=$envValue");

        $listener = new HttpExceptionListener();
        $event = $this->createExceptionEvent(new NotFoundHttpException('Sensitive internal path info'));

        $listener->onKernelException($event);

        $response = json_decode($event->getResponse()->getContent(), true);
        $this->assertSame('Sensitive internal path info', $response['error']);
    }

    public static function debugModeValuesProvider(): array
    {
        return [
            'string 1' => ['1'],
            'string true' => ['true'],
            'string on' => ['on'],
            'string yes' => ['yes'],
            'uppercase TRUE' => ['TRUE'],
            'mixed case True' => ['True'],
        ];
    }

    private function createExceptionEvent(Throwable $exception): ExceptionEvent
    {
        $kernel = $this->createStub(HttpKernelInterface::class);
        $request = Request::create('/api/test');

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);
    }
}
