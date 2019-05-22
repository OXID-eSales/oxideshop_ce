<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapter;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\EventsModuleSettingValidator;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule\ModuleEvents;
use PHPUnit\Framework\TestCase;

class EventsModuleSettingValidatorTest extends TestCase
{
    public function testCanValidate()
    {
        $validator = $this->createValidator();
        
        $eventsModuleSetting = new ModuleSetting(ModuleSetting::EVENTS, []);

        $this->assertTrue(
            $validator->canValidate($eventsModuleSetting)
        );
    }

    public function testCanNotValidate()
    {
        $validator = $this->createValidator();

        $moduleSettingCanNotBeValidated = new ModuleSetting('invalidSetting', []);

        $this->assertFalse(
            $validator->canValidate($moduleSettingCanNotBeValidated)
        );
    }

    public function testValidate()
    {
        $validator = $this->createValidator();

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
        $validator = $this->createValidator();

        $eventsModuleSetting = new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onActivate'   => 'SomeNamespace\\class::noCallableMethod',
                'onDeactivate' => 'SomeNamespace\\class::noCallableMethod'
            ]
        );

        $validator->validate($eventsModuleSetting, 'eventsTestModule', 1);
    }

    /**
     * This is needed only for the modules which has non namespaced classes.
     * This test MUST be removed when support for non namespaced modules will be dropped (metadata v1.*).
     */
    public function testDoNotValidateForNonNamespacedClasses()
    {
        $validator = $this->createValidator();

        $eventsModuleSetting = new ModuleSetting(
            ModuleSetting::EVENTS,
            [
                'onActivate'   => 'class::noCallableMethod',
                'onDeactivate' => 'class::noCallableMethod'
            ]
        );

        $validator->validate($eventsModuleSetting, 'eventsTestModule', 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException
     */
    public function testValidateThrowsExceptionIfNotAbleToValidateSetting()
    {
        $validator = $this->createValidator();

        $moduleSetting = new ModuleSetting(
            'SettingWhichIsNotAbleToBeValidated',
            ['onActivate' => 'MyClass::activate']
        );
        $validator->validate($moduleSetting, 'testModule', 1);
    }

    /**
     * @dataProvider invalidEventsProvider
     *
     * @param array $invalidEvent
     */
    public function testValidateDoesNotValidateSyntax($invalidEvent)
    {
        $validator = $this->createValidator();

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

    /**
     * @return EventsModuleSettingValidator
     */
    private function createValidator(): EventsModuleSettingValidator
    {
        return new EventsModuleSettingValidator(new ShopAdapter());
    }
}
