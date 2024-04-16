<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\State;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ModuleStateServiceTest extends TestCase
{
    use ContainerTrait;

    public function testIsActive(): void
    {
        $configuration = new ModuleConfiguration();
        $configuration
            ->setModuleSource('test')
            ->setActivated(true)
            ->setId('testModule');

        $this->get(ModuleConfigurationDaoInterface::class)->save($configuration, 1);

        $moduleStateService = $this->get(ModuleStateServiceInterface::class);

        $this->assertTrue($moduleStateService->isActive('testModule', 1));
    }
}
