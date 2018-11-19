<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\EventsModuleSettingValidator;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule\ModuleEvents;
use PHPUnit\Framework\TestCase;

class EventsModuleSettingValidatorTest extends TestCase
{

    public function testCanValidate()
    {
        $validator = new EventsModuleSettingValidator();
        
        $eventsModuleSetting = new ModuleSetting(ModuleSetting::EVENTS, []);

        $this->assertTrue(
            $validator->canValidate($eventsModuleSetting)
        );
    }

    public function testCanNotValidate()
    {
        $validator = new EventsModuleSettingValidator();

        $moduleSettingCanNotBeValidated = new ModuleSetting('invalidSetting', []);

        $this->assertFalse(
            $validator->canValidate($moduleSettingCanNotBeValidated)
        );
    }

    public function testValidate()
    {
        $validator = new EventsModuleSettingValidator();

        $eventsModuleSetting = new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onActivate'   => ModuleEvents::class . '::onActivate',
                'onDeactivate' => ModuleEvents::class . '::onDeactivate'
            ]
        );

        $validator->validate($eventsModuleSetting, 'eventsTestModule', 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException
     */
    public function testValidateThrowsExceptionIfEventsDefinedAreNotCallable()
    {
        $validator = new EventsModuleSettingValidator();

        $eventsModuleSetting = new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onActivate'   => 'noCallableMethod',
                'onDeactivate' => 'noCallableMethod'
            ]
        );

        $validator->validate($eventsModuleSetting, 'eventsTestModule', 1);
    }

    /**
     * @dataProvider invalidEventsProvider
     *
     * @param array $invalidEvent
     */
    public function testValidateDoesNotValidateSyntax($invalidEvent)
    {
        $validator = new EventsModuleSettingValidator();

        $eventsModuleSetting = new ModuleSetting(
            ModuleSetting::EVENTS,
            $invalidEvent
        );

        $validator->validate($eventsModuleSetting, 'eventsTestModule', 1);
    }

    public function invalidEventsProvider() : array
    {
        return [
            [['invalidEvent'   => 'noCallableMethod']],
            [null]
        ];
    }
}
