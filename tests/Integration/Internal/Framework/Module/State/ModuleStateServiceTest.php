<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\State;

use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleStateServiceTest extends TestCase
{
    use ContainerTrait;

    private $moduleStateService;

    public function setup(): void
    {
        $this->moduleStateService = $this->get(ModuleStateServiceInterface::class);

        /** @var ContextStub $contextStub */
        $contextStub = $this->get(ContextInterface::class);
        $contextStub->setAllShopIds([1,2]);

        if ($this->moduleStateService->isActive('testModuleId', 1)) {
            $this->moduleStateService->setDeactivated('testModuleId', 1);
        }

        if ($this->moduleStateService->isActive('testModuleId', 2)) {
            $this->moduleStateService->setDeactivated('testModuleId', 2);
        }

        parent::setUp();
    }

    public function testSetActive()
    {
        $this->assertFalse(
            $this->moduleStateService->isActive('testModuleId', 1)
        );
        $this->assertFalse(
            $this->moduleStateService->isActive('testModuleId', 2)
        );

        $this->moduleStateService->setActive('testModuleId', 1);
        $this->moduleStateService->setActive('testModuleId', 2);

        $this->assertTrue(
            $this->moduleStateService->isActive('testModuleId', 1)
        );
        $this->assertTrue(
            $this->moduleStateService->isActive('testModuleId', 2)
        );
    }

    public function testSetActiveIfActiveStateIsAlreadySet()
    {
        $this->expectException(\OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateIsAlreadySetException::class);
        $this->moduleStateService->setActive('testModuleId', 1);
        $this->moduleStateService->setActive('testModuleId', 1);
    }

    public function testSetDeactivated()
    {
        $this->moduleStateService->setActive('testModuleId', 1);

        $this->moduleStateService->setDeactivated('testModuleId', 1);

        $this->assertFalse(
            $this->moduleStateService->isActive('testModuleId', 1)
        );
    }

    public function testSetDeactivatedIfActiveStateIsNotSet()
    {
        $this->expectException(
            \OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateIsAlreadySetException::class
        );
        $this->moduleStateService = $this->get(ModuleStateServiceInterface::class);

        $this->moduleStateService->setDeactivated('testModuleId', 1);
    }

}
