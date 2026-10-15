<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeSettingNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingService;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ThemeSettingServiceTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'apex';
    private const ENVIRONMENT = 'staging';

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

    public function testSettingValuesAreResolvedOnceForAllSettings(): void
    {
        $configuration = (new ThemeConfiguration())
            ->setId(self::THEME_ID)
            ->addThemeSetting((new Setting())->setName('logoFile')->setType('str')->setValue('logo.png'));

        $resolver = $this->createMock(ThemeConfigurationResolverInterface::class);
        $resolver->expects($this->once())->method('resolve')->willReturn($configuration);

        $service = $this->createService(resolver: $resolver);

        $this->assertSame('logo.png', $service->getString('logoFile'));
        $this->assertFalse($service->exists('missing'));
    }

    public function testGetReturnsCachedValue(): void
    {
        $cache = $this->createStub(TagAwareCacheInterface::class);
        $cache->method('get')->willReturn(['logoFile' => 'cached.png']);

        $resolver = $this->createMock(ThemeConfigurationResolverInterface::class);
        $resolver->expects($this->never())->method('resolve');

        $service = $this->createService($cache, $resolver);

        $this->assertSame('cached.png', $service->getString('logoFile'));
    }

    public function testCachesSettingValuesOfActiveTheme(): void
    {
        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->with('theme_settings_' . self::ENVIRONMENT . '_' . self::THEME_ID)
            ->willReturnCallback(fn (string $key, callable $callback): array => $callback());

        $service = $this->createServiceWithSetting('logoFile', 'str', 'logo.png', $cache);

        $this->assertSame('logo.png', $service->getString('logoFile'));
    }

    public function testSettingValuesAreReadFromCacheOncePerInstance(): void
    {
        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())->method('get')->willReturn(['logoFile' => 'logo.png']);

        $service = $this->createService($cache);
        $service->getString('logoFile');

        $this->assertTrue($service->exists('logoFile'));
    }

    public function testMissingThemeConfigurationIsNotCached(): void
    {
        $resolver = $this->createMock(ThemeConfigurationResolverInterface::class);
        $resolver->expects($this->exactly(2))
            ->method('resolve')
            ->willThrowException(new ThemeConfigurationNotFoundException());

        $service = $this->createService(resolver: $resolver);

        $this->assertFalse($service->exists('logoFile'));
        $this->assertFalse($service->exists('logoFile'));
    }

    public function testExistsReturnsTrueForExistingSetting(): void
    {
        $service = $this->createServiceWithSetting('logoFile', 'str', 'logo.png');

        $this->assertTrue($service->exists('logoFile'));
    }

    public function testExistsReturnsTrueFromCacheWithoutResolving(): void
    {
        $cache = $this->createStub(TagAwareCacheInterface::class);
        $cache->method('get')->willReturn(['logoFile' => 'logo.png']);

        $resolver = $this->createMock(ThemeConfigurationResolverInterface::class);
        $resolver->expects($this->never())->method('resolve');

        $service = $this->createService($cache, $resolver);

        $this->assertTrue($service->exists('logoFile'));
    }

    public function testExistsReturnsFalseForMissingSettingWithoutThrowing(): void
    {
        $service = $this->createServiceWithoutSetting();

        $this->assertFalse($service->exists('missing'));
    }

    public function testExistsReturnsFalseWhenActiveThemeConfigurationIsMissing(): void
    {
        $resolver = $this->createStub(ThemeConfigurationResolverInterface::class);
        $resolver->method('resolve')->willThrowException(new ThemeConfigurationNotFoundException());

        $this->assertFalse($this->createService(resolver: $resolver)->exists('logoFile'));
    }

    private function createServiceWithSetting(
        string $name,
        string $type,
        mixed $value,
        ?TagAwareCacheInterface $cache = null,
    ): ThemeSettingService {
        $setting = (new Setting())->setName($name)->setType($type)->setValue($value);
        $configuration = (new ThemeConfiguration())->setId(self::THEME_ID)->addThemeSetting($setting);

        $resolver = $this->createStub(ThemeConfigurationResolverInterface::class);
        $resolver->method('resolve')->willReturn($configuration);

        return $this->createService($cache, $resolver);
    }

    private function createServiceWithoutSetting(): ThemeSettingService
    {
        $resolver = $this->createStub(ThemeConfigurationResolverInterface::class);
        $resolver->method('resolve')->willReturn((new ThemeConfiguration())->setId(self::THEME_ID));

        return $this->createService(resolver: $resolver);
    }

    private function createService(
        ?TagAwareCacheInterface $cache = null,
        ?ThemeConfigurationResolverInterface $resolver = null,
    ): ThemeSettingService {
        return new ThemeSettingService(
            $this->createContext(),
            $resolver ?? $this->createStub(ThemeConfigurationResolverInterface::class),
            $cache ?? new TagAwareAdapter(new ArrayAdapter()),
            self::THEME_ID,
            self::ENVIRONMENT,
        );
    }

    private function createContext(): ContextInterface
    {
        $context = $this->createStub(ContextInterface::class);
        $context->method('getCurrentShopId')->willReturn(self::SHOP_ID);

        return $context;
    }
}
