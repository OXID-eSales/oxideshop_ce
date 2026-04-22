<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestBundle;

use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures\TestBundle\CompilerPass\TestCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TestBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TestCompilerPass());
    }
}
