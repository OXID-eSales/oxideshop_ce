<?php

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Kernel\Fixtures;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class ShopKernel extends TestKernel
{
    private int $overrideShopId;

    public function __construct(string $environment, bool $debug, int $shopId)
    {
        $this->overrideShopId = $shopId;
        parent::__construct($environment, $debug);
    }

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->setParameter('oxid_esales.current_shop_id', $this->overrideShopId);
        $container->setParameter('test.shop_id', $this->overrideShopId);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/oxid_kernel_test/shop_' . $this->overrideShopId;
    }
}
