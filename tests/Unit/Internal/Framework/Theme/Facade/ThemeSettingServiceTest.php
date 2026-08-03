<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\CacheItemNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Cache\ThemeSettingCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Inheritance\ThemeInheritance;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingService;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Exception\ThemeSettingNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ActiveTheme;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\ThemeStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;

final class ThemeSettingServiceTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'apex';

    public function testGetStringReturnsSetting(): void
    {
        $service = $this->createServiceWithSetting('logoFile', 'str', 'logo.png');

        $this->assertSame('logo.png', $service->getString('logoFile'));
    }

    public function testGetBooleanReturnsSetting(): void
    {
        $service = $this->createServiceWithSetting('blShowWishlist', 'bool', true);

        $this->assertTrue($service->getBoolean('blShowWishlist'));
    }

    public function testGetIntegerReturnsSetting(): void
    {
        $service = $this->createServiceWithSetting('iNewArticles', 'int', 5);

        $this->assertSame(5, $service->getInteger('iNewArticles'));
    }

    public function testGetFloatReturnsSetting(): void
    {
        $service = $this->createServiceWithSetting('fSomeFloat', 'num', 1.5);

        $this->assertSame(1.5, $service->getFloat('fSomeFloat'));
    }

    public function testGetCollectionReturnsSetting(): void
    {
        $service = $this->createServiceWithSetting('aSizes', 'arr', ['100x100', '200x200']);

        $this->assertSame(['100x100', '200x200'], $service->getCollection('aSizes'));
    }

    public function testGetIntegerCastsStringValue(): void
    {
        $service = $this->createServiceWithSetting('iNewArticles', 'str', '5');

        $this->assertSame(5, $service->getInteger('iNewArticles'));
    }

    public function testGetFloatCastsStringValue(): void
    {
        $service = $this->createServiceWithSetting('fSomeFloat', 'str', '1.5');

        $this->assertSame(1.5, $service->getFloat('fSomeFloat'));
    }

    public function testGetBooleanCastsStringValue(): void
    {
        $service = $this->createServiceWithSetting('blShowWishlist', 'str', '1');

        $this->assertTrue($service->getBoolean('blShowWishlist'));
    }

    public function testGetStringReturnsEmptyStringForNullValuedSetting(): void
    {
        $service = $this->createServiceWithSetting('sOptional', 'str', null);

        $this->assertSame('', $service->getString('sOptional'));
    }

    public function testGetBooleanReturnsFalseForNullValuedSetting(): void
    {
        $service = $this->createServiceWithSetting('blOptional', 'bool', null);

        $this->assertFalse($service->getBoolean('blOptional'));
    }

    public function testGetIntegerReturnsZeroForNullValuedSetting(): void
    {
        $service = $this->createServiceWithSetting('iOptional', 'int', null);

        $this->assertSame(0, $service->getInteger('iOptional'));
    }

    public function testNullValuedSettingExistsAndGetterDoesNotThrow(): void
    {
        $service = $this->createServiceWithSetting('sOptional', 'str', null);

        $this->assertTrue($service->exists('sOptional'));
        $this->assertSame('', $service->getString('sOptional'));
    }

    public function testGetThrowsForMissingSetting(): void
    {
        $service = $this->createServiceWithoutSetting();

        $this->expectException(ThemeSettingNotFoundException::class);
        $service->getString('missing');
    }

    public function testGetThrowsForMissingThemeConfigurationWithoutResolverCall(): void
    {
        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao
            ->expects($this->once())
            ->method('exists')
            ->with(self::THEME_ID, self::SHOP_ID)
            ->willReturn(false);
        $resolver = $this->createMock(ThemeConfigurationResolverInterface::class);
        $resolver->expects($this->never())->method('resolve');

        $service = $this->createService($dao, resolver: $resolver);

        $this->expectException(ThemeSettingNotFoundException::class);
        $service->getString('logoFile');
    }

    public function testMissingSettingIsCached(): void
    {
        $configuration = (new ThemeConfiguration())->setId(self::THEME_ID);

        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);
        $resolver = $this->createMock(ThemeConfigurationResolverInterface::class);
        $resolver->expects($this->once())->method('resolve')->willReturn($configuration);

        $service = $this->createService($dao, resolver: $resolver);

        $this->assertFalse($service->exists('missing'));
        $this->assertFalse($service->exists('missing'));
    }

    public function testGetReturnsCachedValue(): void
    {
        $cache = $this->createStub(ThemeSettingCacheInterface::class);
        $cache->method('get')->willReturn(['exists' => true, 'value' => 'cached.png']);

        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->never())->method('exists');
        $resolver = $this->createMock(ThemeConfigurationResolverInterface::class);
        $resolver->expects($this->never())->method('resolve');

        $service = $this->createService($dao, $cache, $resolver);

        $this->assertSame('cached.png', $service->getString('logoFile'));
    }

    public function testGetPopulatesCacheOnMiss(): void
    {
        $cache = $this->createMock(ThemeSettingCacheInterface::class);
        $cache->method('get')->willThrowException(new CacheItemNotFoundException('miss'));
        $cache->expects($this->once())->method('put')
            ->with(
                'theme-' . self::THEME_ID . '-setting-logoFile',
                ['exists' => true, 'value' => 'logo.png']
            );

        $service = $this->createServiceWithSetting('logoFile', 'str', 'logo.png', $cache);

        $service->getString('logoFile');
    }

    public function testExistsReturnsTrueForExistingSetting(): void
    {
        $service = $this->createServiceWithSetting('logoFile', 'str', 'logo.png');

        $this->assertTrue($service->exists('logoFile'));
    }

    public function testExistsReturnsTrueFromCacheWithoutHittingDao(): void
    {
        $cache = $this->createStub(ThemeSettingCacheInterface::class);
        $cache->method('get')->willReturn(['exists' => true, 'value' => 'logo.png']);

        $dao = $this->createMock(ThemeConfigurationDaoInterface::class);
        $dao->expects($this->never())->method('exists');
        $resolver = $this->createMock(ThemeConfigurationResolverInterface::class);
        $resolver->expects($this->never())->method('resolve');

        $service = $this->createService($dao, $cache, $resolver);

        $this->assertTrue($service->exists('logoFile'));
    }

    public function testExistsReturnsFalseForMissingSettingWithoutThrowing(): void
    {
        $service = $this->createServiceWithoutSetting();

        $this->assertFalse($service->exists('missing'));
    }

    public function testExistsReturnsFalseForMissingThemeConfiguration(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(false);

        $service = $this->createService($dao);

        $this->assertFalse($service->exists('logoFile'));
    }

    public function testInheritsSettingFromParentThemeWhenChildDoesNotOverrideIt(): void
    {
        $childConfiguration = (new ThemeConfiguration())->setId('child');
        $parentSetting = (new Setting())->setName('sLogoFile')->setType('str')->setValue('parent-logo.png');
        $parentConfiguration = (new ThemeConfiguration())->setId('parent')->addThemeSetting($parentSetting);

        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);

        $resolver = $this->createStub(ThemeConfigurationResolverInterface::class);
        $resolver->method('resolve')->willReturnCallback(
            fn(string $themeId) => $themeId === 'child' ? $childConfiguration : $parentConfiguration
        );

        $themeStateService = $this->createStub(ThemeStateServiceInterface::class);
        $themeStateService->method('getActiveTheme')->willReturn(new ActiveTheme(new ThemeInheritance('child', 'parent')));

        $service = new ThemeSettingService($this->createContext(), $dao, $resolver, $this->createCacheMiss(), $themeStateService);

        $this->assertSame('parent-logo.png', $service->getString('sLogoFile'));
    }

    public function testChildThemeOverridesParentThemeSetting(): void
    {
        $childSetting = (new Setting())->setName('sLogoFile')->setType('str')->setValue('child-logo.png');
        $childConfiguration = (new ThemeConfiguration())->setId('child')->addThemeSetting($childSetting);
        $parentSetting = (new Setting())->setName('sLogoFile')->setType('str')->setValue('parent-logo.png');
        $parentConfiguration = (new ThemeConfiguration())->setId('parent')->addThemeSetting($parentSetting);

        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);

        $resolver = $this->createStub(ThemeConfigurationResolverInterface::class);
        $resolver->method('resolve')->willReturnCallback(
            fn(string $themeId) => $themeId === 'child' ? $childConfiguration : $parentConfiguration
        );

        $themeStateService = $this->createStub(ThemeStateServiceInterface::class);
        $themeStateService->method('getActiveTheme')->willReturn(new ActiveTheme(new ThemeInheritance('child', 'parent')));

        $service = new ThemeSettingService($this->createContext(), $dao, $resolver, $this->createCacheMiss(), $themeStateService);

        $this->assertSame('child-logo.png', $service->getString('sLogoFile'));
    }

    private function createServiceWithSetting(
        string $name,
        string $type,
        mixed $value,
        ?ThemeSettingCacheInterface $cache = null,
    ): ThemeSettingService {
        $setting = (new Setting())->setName($name)->setType($type)->setValue($value);
        $configuration = (new ThemeConfiguration())->setId(self::THEME_ID)->addThemeSetting($setting);

        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);

        $resolver = $this->createStub(ThemeConfigurationResolverInterface::class);
        $resolver->method('resolve')->willReturn($configuration);

        return $this->createService($dao, $cache, $resolver);
    }

    private function createServiceWithoutSetting(): ThemeSettingService
    {
        $configuration = (new ThemeConfiguration())->setId(self::THEME_ID);

        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);

        $resolver = $this->createStub(ThemeConfigurationResolverInterface::class);
        $resolver->method('resolve')->willReturn($configuration);

        return $this->createService($dao, resolver: $resolver);
    }

    private function createService(
        ThemeConfigurationDaoInterface $dao,
        ?ThemeSettingCacheInterface $cache = null,
        ?ThemeConfigurationResolverInterface $resolver = null,
    ): ThemeSettingService {
        return new ThemeSettingService(
            $this->createContext(),
            $dao,
            $resolver ?? $this->createStub(ThemeConfigurationResolverInterface::class),
            $cache ?? $this->createCacheMiss(),
            $this->createThemeStateService(),
        );
    }

    private function createThemeStateService(): ThemeStateServiceInterface
    {
        $themeStateService = $this->createStub(ThemeStateServiceInterface::class);
        $themeStateService->method('getActiveThemeId')->willReturn(self::THEME_ID);
        $themeStateService->method('getActiveTheme')->willReturn(new ActiveTheme(new ThemeInheritance(self::THEME_ID, null)));
        return $themeStateService;
    }

    private function createContext(): ContextInterface
    {
        $context = $this->createStub(ContextInterface::class);
        $context->method('getCurrentShopId')->willReturn(self::SHOP_ID);
        return $context;
    }

    private function createCacheMiss(): ThemeSettingCacheInterface
    {
        $stored = [];

        $cache = $this->createStub(ThemeSettingCacheInterface::class);
        $cache->method('put')->willReturnCallback(function (string $key, array $data) use (&$stored) {
            $stored[$key] = $data;
        });
        $cache->method('get')->willReturnCallback(function (string $key) use (&$stored) {
            if (!isset($stored[$key])) {
                throw new CacheItemNotFoundException("Cache item with key '$key' not found.");
            }

            return $stored[$key];
        });

        return $cache;
    }
}
