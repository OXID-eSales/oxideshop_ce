<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestBundle\Service;

readonly class GreetingService implements GreetingServiceInterface
{
    public function greet(string $name): string
    {
        return "Hello, {$name}!";
    }
}
