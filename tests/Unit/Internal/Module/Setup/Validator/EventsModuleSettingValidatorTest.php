<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapter;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\EventsValidator;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\TestData\TestModule\ModuleEvents;
use PHPUnit\Framework\TestCase;

class EventsModuleSettingValidatorTest extends TestCase
{
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

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting($eventsModuleSetting);

        $validator->validate($moduleConfiguration, 1);
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

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting($eventsModuleSetting);

        $validator->validate($moduleConfiguration, 1);
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

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting($eventsModuleSetting);

        $validator->validate($moduleConfiguration, 1);
    }

    /**
     * @dataProvider invalidEventsProvider
     *
     * @param array $invalidEvent
     *
     * @throws \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException
     */
    public function testValidateDoesNotValidateSyntax($invalidEvent)
    {
        $validator = $this->createValidator();

        $eventsModuleSetting = new ModuleSetting(
            ModuleSetting::EVENTS,
            $invalidEvent
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting($eventsModuleSetting);

        $validator->validate($moduleConfiguration, 1);
    }

    public function invalidEventsProvider() : array
    {
        return [
            [['invalidEvent'   => 'noCallableMethod']],
            [null]
        ];
    }

    /**
     * @return EventsValidator
     */
    private function createValidator(): EventsValidator
    {
        return new EventsValidator(new ShopAdapter());
    }
}
