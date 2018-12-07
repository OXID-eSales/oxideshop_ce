<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\State;

use OxidEsales\EshopCommunity\Internal\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleStateServiceTest extends TestCase
{
    use ContainerTrait;

    public function testSetActive()
    {
        $moduleStateService = $this->get(ModuleStateServiceInterface::class);

        $this->assertFalse(
            $moduleStateService->isActive('testModuleId', 1)
        );

        $moduleStateService->setActive('testModuleId', 1);

        $this->assertTrue(
            $moduleStateService->isActive('testModuleId', 1)
        );
    }

    public function testSetDeactivated()
    {
        $moduleStateService = $this->get(ModuleStateServiceInterface::class);

        $moduleStateService->setActive('testModuleId', 1);

        $moduleStateService->setDeactivated('testModuleId', 1);

        $this->assertFalse(
            $moduleStateService->isActive('testModuleId', 1)
        );
    }
}
