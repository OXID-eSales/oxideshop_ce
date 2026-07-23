<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationService;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityCheckerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ThemeActivationServiceTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testActivateSetsTargetThemeAsActivated(): void
    {
        $targetConfiguration = (new ThemeConfiguration())->setId('target');

        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willReturn($targetConfiguration);
        $dao->method('getAll')->willReturn([]);
        $dao->expects($this->once())->method('save')->with($targetConfiguration, self::SHOP_ID);

        $this->createService($dao)->activate('target', self::SHOP_ID);

        $this->assertTrue($targetConfiguration->isActivated());
    }

    public function testActivateDeactivatesPreviouslyActiveTheme(): void
    {
        $previousConfiguration = (new ThemeConfiguration())->setId('previous')->setActivated(true);
        $targetConfiguration = (new ThemeConfiguration())->setId('target');

        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willReturn($targetConfiguration);
        $dao->method('getAll')->willReturn([
            'previous' => $previousConfiguration,
            'target' => $targetConfiguration,
        ]);

        $this->createService($dao)->activate('target', self::SHOP_ID);

        $this->assertFalse($previousConfiguration->isActivated());
        $this->assertTrue($targetConfiguration->isActivated());
    }

    public function testActivateDispatchesThemeActivatedEvent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willReturn(new ThemeConfiguration());
        $dao->method('getAll')->willReturn([]);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(new ThemeActivatedEvent(self::SHOP_ID, 'target'));

        (new ThemeActivationService(
            $dao,
            $eventDispatcher,
            $this->createStub(ThemeParentCompatibilityCheckerInterface::class)
        ))->activate('target', self::SHOP_ID);
    }

    public function testActivateThrowsWhenThemeConfigurationIsMissing(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willThrowException(new ThemeConfigurationNotFoundException());

        $this->expectException(ThemeConfigurationNotFoundException::class);

        $this->createService($dao)->activate('unknown', self::SHOP_ID);
    }

    public function testActivateThrowsAndDoesNotSaveWhenIncompatibleWithParentTheme(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->never())->method('save');

        $themeParentCompatibilityChecker = $this->createStub(ThemeParentCompatibilityCheckerInterface::class);
        $themeParentCompatibilityChecker
            ->method('assertCompatible')
            ->willThrowException(new ParentVersionMismatchException());

        $service = new ThemeActivationService(
            $dao,
            $this->createStub(EventDispatcherInterface::class),
            $themeParentCompatibilityChecker
        );

        $this->expectException(ParentVersionMismatchException::class);

        $service->activate('target', self::SHOP_ID);
    }

    private function createService(ThemeConfigurationDaoInterface $dao): ThemeActivationService
    {
        return new ThemeActivationService(
            $dao,
            $this->createStub(EventDispatcherInterface::class),
            $this->createStub(ThemeParentCompatibilityCheckerInterface::class)
        );
    }
}
