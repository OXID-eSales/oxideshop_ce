<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper\ThemeConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use PHPUnit\Framework\TestCase;

final class ThemeConfigurationDataMapperTest extends TestCase
{
    private ThemeConfigurationDataMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new ThemeConfigurationDataMapper();
    }

    public function testFromDataUsesDefaultsForMissingFields(): void
    {
        $config = $this->mapper->fromData([]);

        $this->assertSame('', $config->getTitle());
        $this->assertSame('', $config->getSource());
        $this->assertFalse($config->isActivated());
        $this->assertFalse($config->hasThemeSettings());
    }

    public function testToDataOmitsThemeSettingsWhenNoneAdded(): void
    {
        $data = $this->mapper->toData((new ThemeConfiguration())->setSource('testSourcePath'));

        $this->assertArrayNotHasKey('themeSettings', $data);
        $this->assertSame('', $data['title']);
    }

    public function testToDataOmitsEmptySettingFields(): void
    {
        $setting = (new Setting())
            ->setName('mySetting')
            ->setType('bool')
            ->setValue(true);

        $data = $this->mapper->toData(
            (new ThemeConfiguration())->setSource('testSourcePath')->addThemeSetting($setting)
        );

        $this->assertArrayNotHasKey('group', $data['themeSettings']['mySetting']);
        $this->assertArrayNotHasKey('position', $data['themeSettings']['mySetting']);
        $this->assertArrayNotHasKey('constraints', $data['themeSettings']['mySetting']);
    }

    public function testRoundTrip(): void
    {
        $original = [
            'source'        => 'Application/views/apex',
            'activated'     => true,
            'title'         => 'APEX Theme',
            'themeSettings' => [
                'setting1' => [
                    'type'        => 'select',
                    'value'       => 'a',
                    'group'       => 'layout',
                    'position'    => 1,
                    'constraints' => ['a', 'b'],
                ],
            ],
        ];

        $config = $this->mapper->fromData($original);
        $result = $this->mapper->toData($config);

        $this->assertSame($original, $result);
    }
}
