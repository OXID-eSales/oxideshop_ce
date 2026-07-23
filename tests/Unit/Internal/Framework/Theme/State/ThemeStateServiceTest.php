<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\State;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\ThemeChain;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Chain\ThemeChainResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Event\ThemeActivatedEvent;
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
        $themeChainResolver = $this->createStub(ThemeChainResolverInterface::class);
        $themeChainResolver->method('getThemeChain')->willReturn(new ThemeChain(['active', 'parent']));

        $activeTheme = $this->createService($dao, $themeChainResolver)->getActiveTheme(self::SHOP_ID);

        $this->assertSame('active', $activeTheme->getId());
        $this->assertTrue($activeTheme->getChain()->hasParentTheme());
    }

    public function testGetActiveThemeIsNotChildThemeWhenActiveThemeHasNoParent(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('getAll')->willReturn([
            'active' => (new ThemeConfiguration())->setId('active')->setActivated(true),
        ]);
        $themeChainResolver = $this->createStub(ThemeChainResolverInterface::class);
        $themeChainResolver->method('getThemeChain')->willReturn(new ThemeChain(['active']));

        $this->assertFalse(
            $this->createService($dao, $themeChainResolver)->getActiveTheme(self::SHOP_ID)->getChain()->hasParentTheme()
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
        $themeChainResolver = $this->createMock(ThemeChainResolverInterface::class);
        $themeChainResolver->expects($this->once())->method('getThemeChain')->willReturn(new ThemeChain(['active', 'parent']));
        $service = $this->createService($dao, $themeChainResolver);

        $service->getActiveTheme(self::SHOP_ID);

        $this->assertTrue($service->getActiveTheme(self::SHOP_ID)->getChain()->hasParentTheme());
    }

    public function testServiceIsSubscribedToThemeActivatedEvent(): void
    {
        $this->assertSame(
            [ThemeActivatedEvent::class => 'invalidateActiveThemeCache'],
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
        $themeChainResolver = $this->createMock(ThemeChainResolverInterface::class);
        $themeChainResolver->expects($this->exactly(2))->method('getThemeChain')->willReturn(new ThemeChain(['active']));
        $service = $this->createService($dao, $themeChainResolver);

        $service->getActiveTheme(self::SHOP_ID);
        $service->invalidateActiveThemeCache(new ThemeActivatedEvent(self::SHOP_ID, 'active'));

        $this->assertSame('active', $service->getActiveTheme(self::SHOP_ID)->getId());
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
        ?ThemeChainResolverInterface $themeChainResolver = null,
    ): ThemeStateService {
        return new ThemeStateService($dao, $themeChainResolver ?? $this->createStub(ThemeChainResolverInterface::class));
    }
}
