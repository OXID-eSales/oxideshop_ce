<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Console\CommandsProvider;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\Command\ClearCacheCommand;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Cache\TemplateCacheService;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use PHPUnit\Framework\TestCase;

class ClearCacheCommandTest extends TestCase
{
    public function testClearCacheTriggersRegularAndTemplatesCleaners()
    {
        $shopAdapterMock = $this->createMock(ShopAdapterInterface::class);
        $shopAdapterMock->expects($this->once())->method('invalidateModulesCache');

        $templateCacheServiceMock = $this->createMock(TemplateCacheService::class);
        $templateCacheServiceMock->expects($this->once())->method('invalidateTemplateCache');

        $containerCacheMock = $this->createMock(ContainerCacheInterface::class);
        $containerCacheMock->expects($this->once())->method('invalidate');

        $command = new ClearCacheCommand(
            $shopAdapterMock,
            $templateCacheServiceMock,
            $containerCacheMock
        );

        $command->run(
            $this->createMock(\Symfony\Component\Console\Input\InputInterface::class),
            $this->createMock(\Symfony\Component\Console\Output\OutputInterface::class),
        );
    }
}
