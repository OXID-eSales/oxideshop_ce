<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\Form;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Form\SettingValueMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SettingValueMapperTest extends TestCase
{
    private SettingValueMapper $mapper;

    public function setUp(): void
    {
        $this->mapper = new SettingValueMapper();
    }

    #[DataProvider('formValueProvider')]
    public function testToFormValue(string $type, mixed $value, bool|string $expectedFormValue): void
    {
        $this->assertSame(
            $expectedFormValue,
            $this->mapper->toFormValue($this->createSetting($type, $value))
        );
    }

    public static function formValueProvider(): array
    {
        return [
            'bool true' => ['bool', true, true],
            'bool falsy string' => ['bool', '', false],
            'string' => ['str', 'some value', 'some value'],
            'select' => ['select', 'option1', 'option1'],
            'number' => ['num', 1.5, '1.5'],
            'null value' => ['str', null, ''],
            'collection' => ['arr', ['first', 'second'], "first\nsecond"],
            'associative collection' => ['aarr', ['a' => '1', 'b' => '2'], "a => 1\nb => 2"],
            'associative collection skips non scalar entries' => ['aarr', ['a' => '1', 'b' => ['nested']], 'a => 1'],
        ];
    }

    #[DataProvider('settingValueProvider')]
    public function testFromFormValues(string $type, string $formValue, mixed $expectedValue): void
    {
        $configuration = (new ThemeConfiguration())->addThemeSetting($this->createSetting($type, null));

        $this->assertSame(
            ['testSetting' => $expectedValue],
            $this->mapper->fromFormValues($configuration, ['testSetting' => $formValue])
        );
    }

    public static function settingValueProvider(): array
    {
        return [
            'bool true' => ['bool', 'true', true],
            'bool false' => ['bool', 'false', false],
            'bool numeric' => ['bool', '1', true],
            'string' => ['str', 'some value', 'some value'],
            'select' => ['select', 'option2', 'option2'],
            'integer number' => ['num', '42', 42],
            'float number' => ['num', '1.5', 1.5],
            'non numeric number input stays unchanged' => ['num', '1,5', '1,5'],
            'collection' => ['arr', "first\n second \n\nthird", ['first', 'second', 'third']],
            'empty collection' => ['arr', "\n\n", []],
            'associative collection' => ['aarr', "a => 1\n b => 2 ", ['a' => '1', 'b' => '2']],
            'associative collection splits at last arrow' => ['aarr', 'a => b => c', ['a => b' => 'c']],
            'associative collection skips invalid lines' => ['aarr', "broken line\n => empty key\na => 1", ['a' => '1']],
        ];
    }

    public function testFormValueRoundTripKeepsSettingValue(): void
    {
        $setting = $this->createSetting('aarr', ['size' => '100*100', 'zoom' => '800*800']);
        $configuration = (new ThemeConfiguration())->addThemeSetting($setting);

        $formValue = $this->mapper->toFormValue($setting);

        $this->assertSame(
            ['testSetting' => ['size' => '100*100', 'zoom' => '800*800']],
            $this->mapper->fromFormValues($configuration, ['testSetting' => (string) $formValue])
        );
    }

    private function createSetting(string $type, mixed $value): Setting
    {
        return (new Setting())->setName('testSetting')->setType($type)->setValue($value);
    }
}
