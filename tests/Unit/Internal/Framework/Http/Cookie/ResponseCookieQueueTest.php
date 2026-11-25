<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http\Cookie;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Cookie\ResponseCookieQueue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;

final class ResponseCookieQueueTest extends TestCase
{
    public function testGetAllReturnsCookiesInOrderOfAddition(): void
    {
        $queue = new ResponseCookieQueue();
        $first = Cookie::create('first');
        $second = Cookie::create('second');

        $queue->add($first);
        $queue->add($second);

        $this->assertSame([$first, $second], $queue->getAll());
    }

    public function testClearDiscardsCookies(): void
    {
        $queue = new ResponseCookieQueue();
        $queue->add(Cookie::create('some-cookie'));

        $queue->clear();

        $this->assertSame([], $queue->getAll());
    }
}
