<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Filesystem\Path;

final class ThemeEnvironmentConfigurationDaoTest extends IntegrationTestCase
{
    private const THEME_ID = 'testTheme';
    private const SHOP_ID = 1;

    public function testGetReturnsEmptyConfigurationForMissingFile(): void
    {
        $configuration = $this->get(ThemeEnvironmentConfigurationDaoInterface::class)
            ->get(self::THEME_ID, self::SHOP_ID);

        $this->assertSame([], $configuration->getSettingValues());
    }

    public function testGetReturnsNormalizedSettingValues(): void
    {
        $this->saveEnvironmentConfiguration([
            'themeSettings' => [
                'stringSetting' => ['value' => 'value'],
                'emptyStringSetting' => ['value' => ''],
                'booleanSetting' => ['value' => false],
                'integerSetting' => ['value' => 0],
                'arraySetting' => ['value' => ['first', 'second']],
            ],
        ]);

        $configuration = $this->get(ThemeEnvironmentConfigurationDaoInterface::class)
            ->get(self::THEME_ID, self::SHOP_ID);

        $this->assertSame(
            [
                'stringSetting' => 'value',
                'emptyStringSetting' => '',
                'booleanSetting' => false,
                'integerSetting' => 0,
                'arraySetting' => ['first', 'second'],
            ],
            $configuration->getSettingValues()
        );
    }

    public function testGetReportsInvalidConfigurationWithFilePath(): void
    {
        $path = $this->getEnvironmentConfigurationPath();
        $this->saveEnvironmentConfiguration([
            'themeSettings' => [
                'testSetting' => [
                    'value' => 'value',
                    'type' => 'str',
                ],
            ],
        ]);

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('File ' . $path . ' is broken:');

        $this->get(ThemeEnvironmentConfigurationDaoInterface::class)
            ->get(self::THEME_ID, self::SHOP_ID);
    }

    private function saveEnvironmentConfiguration(array $configuration): void
    {
        $this->get(FileStorageFactoryInterface::class)
            ->create($this->getEnvironmentConfigurationPath())
            ->save($configuration);
    }

    private function getEnvironmentConfigurationPath(): string
    {
        return Path::join(
            EnvUrlFormatter::toEnvUrl(
                $this->get(BasicContextInterface::class)->getProjectConfigurationDirectory()
            ),
            'shops',
            (string) self::SHOP_ID,
            'themes',
            self::THEME_ID . '.yaml'
        );
    }
}
