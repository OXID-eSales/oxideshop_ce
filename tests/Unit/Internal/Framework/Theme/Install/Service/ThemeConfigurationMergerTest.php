<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationMerger;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use PHPUnit\Framework\TestCase;

final class ThemeConfigurationMergerTest extends TestCase
{
    private ThemeConfigurationMerger $merger;

    protected function setUp(): void
    {
        $this->merger = new ThemeConfigurationMerger();
    }

    public function testMergePreservesActivatedState(): void
    {
        $existing = (new ThemeConfiguration())->setActivated(true);
        $result = $this->merger->merge(new ThemeConfiguration(), $existing);

        $this->assertTrue($result->isActivated());
    }

    public function testMergePreservesCustomisedSettingValues(): void
    {
        $existing = $this->buildConfiguration(['testSetting' => 'customValue']);
        $incoming = $this->buildConfiguration(['testSetting' => 'defaultValue']);

        $result = $this->merger->merge($incoming, $existing);

        $this->assertSettingValue('customValue', 'testSetting', $result);
    }

    public function testMergeUsesDefaultValueForNewSettings(): void
    {
        $existing = new ThemeConfiguration();
        $incoming = $this->buildConfiguration(['testSetting' => 'defaultValue']);

        $result = $this->merger->merge($incoming, $existing);

        $this->assertSettingValue('defaultValue', 'testSetting', $result);
    }

    public function testMergeUsesIncomingSettingType(): void
    {
        $existingSetting = (new Setting())->setName('testSetting')->setType('bool')->setValue('customValue');
        $existing = (new ThemeConfiguration())->addThemeSetting($existingSetting);

        $incomingSetting = (new Setting())->setName('testSetting')->setType('str')->setValue('defaultValue');
        $incoming = (new ThemeConfiguration())->addThemeSetting($incomingSetting);

        $result = $this->merger->merge($incoming, $existing);

        $this->assertSame('str', $result->getSettingByName('testSetting')->getType());
    }

    private function buildConfiguration(array $settingValues): ThemeConfiguration
    {
        $config = new ThemeConfiguration();

        foreach ($settingValues as $name => $value) {
            $config->addThemeSetting((new Setting())->setName($name)->setType('str')->setValue($value));
        }

        return $config;
    }

    private function assertSettingValue(mixed $expected, string $name, ThemeConfiguration $config): void
    {
        $setting = $config->getSettingByName($name);
        $this->assertNotNull($setting, "Setting '$name' not found in configuration");
        $this->assertSame($expected, $setting->getValue());
    }
}