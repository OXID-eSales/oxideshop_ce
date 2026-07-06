<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReady;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReadyEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Http\ResponseReadyListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

final class ResponseReadyListenerTest extends TestCase
{
    public function testStopsTheRequestCarryingTheEventResponse(): void
    {
        $response = new Response('done');

        try {
            (new ResponseReadyListener())->stopRequest(new ResponseReadyEvent($response));
            $this->fail('Request was not stopped');
        } catch (ResponseReady $signal) {
            $this->assertSame($response, $signal->getResponse());
        }
    }

    public function testCarriesAReplacedResponse(): void
    {
        $event = new ResponseReadyEvent(new Response('original'));
        $replacement = new Response('replaced');
        $event->setResponse($replacement);

        try {
            (new ResponseReadyListener())->stopRequest($event);
            $this->fail('Request was not stopped');
        } catch (ResponseReady $signal) {
            $this->assertSame($replacement, $signal->getResponse());
        }
    }

    public function testRunsAfterDefaultPriorityListeners(): void
    {
        $priority = ResponseReadyListener::getSubscribedEvents()[ResponseReadyEvent::class][1];

        $this->assertLessThan(0, $priority);
    }
}
