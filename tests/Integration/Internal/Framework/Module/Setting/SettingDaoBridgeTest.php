<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace Integration\Internal\Framework\Module\Setting;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class SettingDaoBridgeTest extends TestCase
{
    use ContainerTrait;

    public function testSaving(): void
    {
        $bridge = $this->get(SettingDaoBridgeInterface::class);

        $setting = new Setting();
        $setting
            ->setName('testSettingDaoBridgeSetting')
            ->setType('str')
            ->setValue('second')
            ->setConstraints([
                'first',
                'second',
                'third',
            ])
            ->setGroupName('testGroup')
            ->setPositionInGroup(5);

        $bridge->save($setting, 'testModuleId', 1);

        $this->assertEquals(
            $setting,
            $bridge->get('testSettingDaoBridgeSetting', 'testModuleId')
        );
    }
}
