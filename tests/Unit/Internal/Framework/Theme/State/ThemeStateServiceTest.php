<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritanceResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ThemeStateServiceTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testIsActiveReturnsTrueWhenThemeIsActivated(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);
        $dao->method('get')->willReturn((new ThemeConfiguration())->setActivated(true));

        $this->assertTrue($this->createService($dao)->isActive('theme', self::SHOP_ID));
    }

    public function testIsActiveReturnsFalseWhenThemeIsNotActivated(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);
        $dao->method('get')->willReturn(new ThemeConfiguration());

        $this->assertFalse($this->createService($dao)->isActive('theme', self::SHOP_ID));
    }

    public function testIsActiveReturnsFalseWhenThemeDoesNotExist(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(false);

        $this->assertFalse($this->createService($dao)->isActive('unknown', self::SHOP_ID));
    }

    public function testGetActiveThemeIdReturnsIdOfActivatedTheme(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'inactive' => (new ThemeConfiguration())->setId('inactive'),
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);

        $this->assertSame('active', $this->createService($dao)->getActiveThemeId(self::SHOP_ID));
    }

    public function testGetActiveThemeIdThrowsExceptionWhenNoThemeIsActivated(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'theme1' => (new ThemeConfiguration())->setId('theme1'),
            'theme2' => (new ThemeConfiguration())->setId('theme2'),
        ]);

        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createService($dao)->getActiveThemeId(self::SHOP_ID);
    }

    public function testGetActiveThemeIdResolvesThemeOnlyOnce(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->once())->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $service = $this->createService($dao);

        $service->getActiveThemeId(self::SHOP_ID);

        $this->assertSame('active', $service->getActiveThemeId(self::SHOP_ID));
    }

    public function testGetActiveThemeIsChildThemeWhenActiveThemeDeclaresAParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $themeInheritanceResolver = $this->createStub(ThemeInheritanceResolverInterface::class);
        $themeInheritanceResolver->method('resolve')->willReturn(new ThemeInheritance('active', 'parent'));

        $activeTheme = $this->createService($dao, $themeInheritanceResolver)->getActiveTheme(self::SHOP_ID);

        $this->assertSame('active', $activeTheme->getId());
        $this->assertTrue($activeTheme->getInheritance()->hasParentTheme());
    }

    public function testGetActiveThemeIsNotChildThemeWhenActiveThemeHasNoParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $themeInheritanceResolver = $this->createStub(ThemeInheritanceResolverInterface::class);
        $themeInheritanceResolver->method('resolve')->willReturn(new ThemeInheritance('active', null));

        $this->assertFalse(
            $this->createService($dao, $themeInheritanceResolver)->getActiveTheme(self::SHOP_ID)->getInheritance()->hasParentTheme()
        );
    }

    public function testGetActiveThemeThrowsExceptionWhenNoThemeIsActivated(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'theme1' => (new ThemeConfiguration())->setId('theme1'),
        ]);

        $this->expectException(ActiveThemeNotFoundException::class);

        $this->createService($dao)->getActiveTheme(self::SHOP_ID);
    }

    public function testGetActiveThemeResolvesThemeOnlyOnce(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $themeInheritanceResolver = $this->createMock(ThemeInheritanceResolverInterface::class);
        $themeInheritanceResolver->expects($this->once())->method('resolve')->willReturn(new ThemeInheritance('active', 'parent'));
        $service = $this->createService($dao, $themeInheritanceResolver);

        $service->getActiveTheme(self::SHOP_ID);

        $this->assertTrue($service->getActiveTheme(self::SHOP_ID)->getInheritance()->hasParentTheme());
    }

    public function testServiceIsSubscribedToThemeActivatedEvent(): void
    {
        $this->assertSame(
            [
                ThemeActivatedEvent::class => 'invalidateActiveThemeCache',
                ThemeConfigurationChangedEvent::class => 'invalidateActiveThemeCache',
            ],
            ThemeStateService::getSubscribedEvents()
        );
    }

    public function testImplementsEventSubscriberInterface(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->createService($this->createStub(ThemeConfigurationDaoInterface::class)));
    }

    public function testInvalidateActiveThemeCacheForcesActiveThemeIdToBeResolvedAgain(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->exactly(2))->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $service = $this->createService($dao);

        $service->getActiveThemeId(self::SHOP_ID);
        $service->invalidateActiveThemeCache(new ThemeActivatedEvent(self::SHOP_ID, 'active'));

        $this->assertSame('active', $service->getActiveThemeId(self::SHOP_ID));
    }

    public function testInvalidateActiveThemeCacheForcesActiveThemeToBeResolvedAgain(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $themeInheritanceResolver = $this->createMock(ThemeInheritanceResolverInterface::class);
        $themeInheritanceResolver->expects($this->exactly(2))->method('resolve')->willReturn(new ThemeInheritance('active', null));
        $service = $this->createService($dao, $themeInheritanceResolver);

        $service->getActiveTheme(self::SHOP_ID);
        $service->invalidateActiveThemeCache(new ThemeActivatedEvent(self::SHOP_ID, 'active'));

        $this->assertSame('active', $service->getActiveTheme(self::SHOP_ID)->getId());
    }

    public function testInvalidateActiveThemeCacheForcesActiveThemeIdToBeResolvedAgainOnConfigurationChange(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->exactly(2))->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $service = $this->createService($dao);

        $service->getActiveThemeId(self::SHOP_ID);
        $service->invalidateActiveThemeCache(
            new ThemeConfigurationChangedEvent((new ThemeConfiguration())->setId('active'), self::SHOP_ID)
        );

        $this->assertSame('active', $service->getActiveThemeId(self::SHOP_ID));
    }

    public function testInvalidateActiveThemeCacheDoesNotAffectOtherShops(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->once())->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $service = $this->createService($dao);

        $service->getActiveThemeId(self::SHOP_ID);
        $service->invalidateActiveThemeCache(new ThemeActivatedEvent(self::SHOP_ID + 1, 'active'));

        $this->assertSame('active', $service->getActiveThemeId(self::SHOP_ID));
    }

    private function createService(
        ThemeConfigurationDaoInterface $dao,
        ?ThemeInheritanceResolverInterface $themeInheritanceResolver = null,
    ): ThemeStateService {
        return new ThemeStateService($dao, $themeInheritanceResolver ?? $this->createStub(ThemeInheritanceResolverInterface::class));
    }
}
