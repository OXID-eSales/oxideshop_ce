<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ResponseReadyTest extends TestCase
{
    public function testCarriesTheGivenResponse(): void
    {
        $response = new Response('body', Response::HTTP_NOT_FOUND);

        $this->assertSame($response, (new ResponseReady($response))->getResponse());
    }

    public function testIsAThrowableButNotAnException(): void
    {
        $signal = new ResponseReady(new Response());

        $this->assertInstanceOf(\Throwable::class, $signal);
        $this->assertNotInstanceOf(\Exception::class, $signal);
    }

    public function testIsNotCaughtByCatchException(): void
    {
        $response = new Response('body');

        $caughtByException = false;
        $caughtBySignal = null;
        try {
            throw new ResponseReady($response);
        } catch (\Exception) {
            $caughtByException = true;
        } catch (ResponseReady $signal) {
            $caughtBySignal = $signal;
        }

        $this->assertFalse($caughtByException);
        $this->assertSame($response, $caughtBySignal?->getResponse());
    }
}
