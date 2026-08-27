<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class ThemeConfigurationResolverTest extends IntegrationTestCase
{
    private const THEME_ID = 'testTheme';
    private const SHOP_ID = 1;
    private const SETTING_NAME = 'testSetting';

    public function testResolveAppliesEnvironmentValueWithoutChangingCanonicalConfiguration(): void
    {
        $this->saveCanonicalConfiguration();
        $this->saveEnvironmentConfiguration('environmentValue');

        $resolvedConfiguration = $this->get(ThemeConfigurationResolverInterface::class)
            ->resolve(self::THEME_ID, self::SHOP_ID);
        $canonicalConfiguration = $this->get(ThemeConfigurationDaoInterface::class)
            ->get(self::THEME_ID, self::SHOP_ID);

        $this->assertSame(
            'environmentValue',
            $resolvedConfiguration->getSettingByName(self::SETTING_NAME)->getValue()
        );
        $this->assertSame(
            'canonicalValue',
            $canonicalConfiguration->getSettingByName(self::SETTING_NAME)->getValue()
        );
    }

    public function testResolveCachesConfigurationAndReturnsIndependentClones(): void
    {
        $this->saveCanonicalConfiguration();
        $this->saveEnvironmentConfiguration('firstEnvironmentValue');
        $resolver = $this->get(ThemeConfigurationResolverInterface::class);

        $first = $resolver->resolve(self::THEME_ID, self::SHOP_ID);
        $this->saveEnvironmentConfiguration('changedDuringRequest');
        $first->getSettingByName(self::SETTING_NAME)->setValue('mutatedResolvedValue');
        $second = $resolver->resolve(self::THEME_ID, self::SHOP_ID);

        $this->assertNotSame($first, $second);
        $this->assertNotSame(
            $first->getSettingByName(self::SETTING_NAME),
            $second->getSettingByName(self::SETTING_NAME)
        );
        $this->assertSame(
            'firstEnvironmentValue',
            $second->getSettingByName(self::SETTING_NAME)->getValue()
        );
    }

    public function testConfigurationChangeEvictsResolvedConfigurationCache(): void
    {
        $this->saveCanonicalConfiguration();
        $this->saveEnvironmentConfiguration('firstEnvironmentValue');
        $resolver = $this->get(ThemeConfigurationResolverInterface::class);

        $this->assertSame(
            'firstEnvironmentValue',
            $resolver
                ->resolve(self::THEME_ID, self::SHOP_ID)
                ->getSettingByName(self::SETTING_NAME)
                ->getValue()
        );

        $this->saveEnvironmentConfiguration('changedEnvironmentValue');
        $configurationDao = $this->get(ThemeConfigurationDaoInterface::class);
        $configurationDao->save(
            $configurationDao->get(self::THEME_ID, self::SHOP_ID),
            self::SHOP_ID
        );

        $this->assertSame(
            'changedEnvironmentValue',
            $resolver
                ->resolve(self::THEME_ID, self::SHOP_ID)
                ->getSettingByName(self::SETTING_NAME)
                ->getValue()
        );
    }

    private function saveCanonicalConfiguration(): void
    {
        $configuration = (new ThemeConfiguration())
            ->setId(self::THEME_ID)
            ->setSource(Path::makeRelative(
                __DIR__ . '/Fixtures/testTheme',
                $this->get(BasicContextInterface::class)->getShopRootPath()
            ))
            ->addThemeSetting(
                (new Setting())
                    ->setName(self::SETTING_NAME)
                    ->setType('str')
                    ->setValue('canonicalValue')
            );

        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, self::SHOP_ID);
    }

    private function saveEnvironmentConfiguration(string $value): void
    {
        $path = Path::join(
            EnvUrlFormatter::toEnvUrl(
                $this->get(BasicContextInterface::class)->getProjectConfigurationDirectory()
            ),
            'shops',
            (string) self::SHOP_ID,
            'themes',
            self::THEME_ID . '.yaml'
        );

        $this->get(FileStorageFactoryInterface::class)->create($path)->save([
            'themeSettings' => [
                self::SETTING_NAME => ['value' => $value],
            ],
        ]);
    }
}
