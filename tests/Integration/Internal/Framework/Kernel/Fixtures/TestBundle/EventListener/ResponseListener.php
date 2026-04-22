<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestBundle\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::RESPONSE)]
readonly class ResponseListener
{
    public function __invoke(ResponseEvent $event): void
    {
        $event->getResponse()->headers->set('X-Test-Bundle', 'active');
    }
}
