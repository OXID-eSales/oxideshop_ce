<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestBundle\Service;

interface GreetingServiceInterface
{
    public function greet(string $name): string;
}
