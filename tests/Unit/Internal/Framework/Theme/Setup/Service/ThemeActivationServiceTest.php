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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritanceResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeParentVersionMismatchException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\Exception\ThemeMetaDataInvalidException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationService;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeParentCompatibilityCheckerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ThemeActivationServiceTest extends TestCase
{
    private const SHOP_ID = 1;
    private const PARENT_THEME_ID = 'apex';

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
            $this->createStub(ThemeParentCompatibilityCheckerInterface::class),
            $this->createResolverStub(hasParent: false)
        ))->activate('target', self::SHOP_ID);
    }

    public function testActivateThrowsWhenThemeConfigurationIsMissing(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willThrowException(new ThemeConfigurationNotFoundException());

        $this->expectException(ThemeConfigurationNotFoundException::class);

        $this->createService($dao)->activate('unknown', self::SHOP_ID);
    }

    public function testActivateDoesNotCheckCompatibilityWhenThemeHasNoParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('get')->willReturn((new ThemeConfiguration())->setId('target'));
        $dao->method('getAll')->willReturn([]);

        $themeParentCompatibilityChecker = $this->createMock(ThemeParentCompatibilityCheckerInterface::class);
        $themeParentCompatibilityChecker->expects($this->never())->method('assertCompatible');

        $service = new ThemeActivationService(
            $dao,
            $this->createStub(EventDispatcherInterface::class),
            $themeParentCompatibilityChecker,
            $this->createResolverStub(hasParent: false)
        );

        $service->activate('target', self::SHOP_ID);

        $this->addToAssertionCount(1);
    }

    public function testActivateThrowsAndDoesNotSaveWhenIncompatibleWithParentTheme(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->never())->method('save');

        $themeParentCompatibilityChecker = $this->createStub(ThemeParentCompatibilityCheckerInterface::class);
        $themeParentCompatibilityChecker
            ->method('assertCompatible')
            ->willThrowException(new ThemeParentVersionMismatchException());

        $service = new ThemeActivationService(
            $dao,
            $this->createStub(EventDispatcherInterface::class),
            $themeParentCompatibilityChecker,
            $this->createResolverStub(hasParent: true)
        );

        $this->expectException(ThemeParentVersionMismatchException::class);

        $service->activate('target', self::SHOP_ID);
    }

    public function testActivateThrowsAndDoesNotSaveWhenThemesOwnMetadataIsUnreadable(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->never())->method('save');

        $themeInheritanceResolver = $this->createStub(ThemeInheritanceResolverInterface::class);
        $themeInheritanceResolver->method('resolve')->willThrowException(new InvalidThemeMetaDataException());

        $service = new ThemeActivationService(
            $dao,
            $this->createStub(EventDispatcherInterface::class),
            $this->createStub(ThemeParentCompatibilityCheckerInterface::class),
            $themeInheritanceResolver
        );

        $this->expectException(ThemeMetaDataInvalidException::class);

        $service->activate('target', self::SHOP_ID);
    }

    private function createService(ThemeConfigurationDaoInterface $dao): ThemeActivationService
    {
        return new ThemeActivationService(
            $dao,
            $this->createStub(EventDispatcherInterface::class),
            $this->createStub(ThemeParentCompatibilityCheckerInterface::class),
            $this->createResolverStub(hasParent: false)
        );
    }

    private function createResolverStub(bool $hasParent): ThemeInheritanceResolverInterface
    {
        $themeInheritanceResolver = $this->createStub(ThemeInheritanceResolverInterface::class);
        $themeInheritanceResolver->method('resolve')->willReturn(
            new ThemeInheritance('target', $hasParent ? self::PARENT_THEME_ID : null)
        );

        return $themeInheritanceResolver;
    }
}
