<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Activation\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ParameterDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Exception\ThemeParentCompatibilityException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Service\ThemeActivationService;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Activation\Service\ThemeParentCompatibilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ThemeActivationServiceTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testActivateStoresTargetThemeAsShopParameter(): void
    {
        $parameterDao = $this->createMock(ParameterDaoInterface::class);
        $parameterDao
            ->expects($this->once())
            ->method('add')
            ->with('oxid_esales.theme.active', 'target', self::SHOP_ID);

        $this->createService(parameterDao: $parameterDao)->activate('target', self::SHOP_ID);
    }

    public function testActivateDispatchesThemeActivatedEvent(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with(new ThemeActivatedEvent(self::SHOP_ID, 'target'));

        $this->createService(eventDispatcher: $eventDispatcher)->activate('target', self::SHOP_ID);
    }

    public function testActivateThrowsAndStoresNothingWhenThemeConfigurationIsMissing(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(false);
        $parameterDao = $this->createMock(ParameterDaoInterface::class);
        $parameterDao->expects($this->never())->method('add');

        $this->expectException(ThemeConfigurationNotFoundException::class);

        $this->createService($dao, $parameterDao)->activate('unknown', self::SHOP_ID);
    }

    public function testActivateThrowsAndStoresNothingWhenIncompatibleWithParentTheme(): void
    {
        $parameterDao = $this->createMock(ParameterDaoInterface::class);
        $parameterDao->expects($this->never())->method('add');

        $this->expectException(ThemeParentCompatibilityException::class);

        $this->createService(parameterDao: $parameterDao, checker: $this->createIncompatibleChecker())
            ->activate('target', self::SHOP_ID);
    }

    public function testActivateThrowsAndStoresNothingWhenThemesOwnMetadataIsUnreadable(): void
    {
        $parameterDao = $this->createMock(ParameterDaoInterface::class);
        $parameterDao->expects($this->never())->method('add');

        $this->expectException(InvalidThemeMetaDataException::class);

        $this->createService(parameterDao: $parameterDao, checker: $this->createUnreadableMetaDataChecker())
            ->activate('target', self::SHOP_ID);
    }

    private function createService(
        ?ThemeConfigurationDaoInterface $dao = null,
        ?ParameterDaoInterface $parameterDao = null,
        ?EventDispatcherInterface $eventDispatcher = null,
        ?ThemeParentCompatibilityCheckerInterface $checker = null
    ): ThemeActivationService {
        if ($dao === null) {
            $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
            $dao->method('exists')->willReturn(true);
        }

        return new ThemeActivationService(
            $dao,
            $parameterDao ?? $this->createStub(ParameterDaoInterface::class),
            $eventDispatcher ?? $this->createStub(EventDispatcherInterface::class),
            $checker ?? $this->createStub(ThemeParentCompatibilityCheckerInterface::class)
        );
    }

    private function createIncompatibleChecker(): ThemeParentCompatibilityCheckerInterface
    {
        $checker = $this->createStub(ThemeParentCompatibilityCheckerInterface::class);
        $checker->method('validate')->willThrowException(new ThemeParentCompatibilityException());

        return $checker;
    }

    private function createUnreadableMetaDataChecker(): ThemeParentCompatibilityCheckerInterface
    {
        $checker = $this->createStub(ThemeParentCompatibilityCheckerInterface::class);
        $checker->method('validate')->willThrowException(new InvalidThemeMetaDataException());

        return $checker;
    }
}
