<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Validator;

use function is_array;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ControllersDuplicationModuleSettingException;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException;

/**
 * @internal
 */
class EventsModuleSettingValidator implements ModuleSettingValidatorInterface
{
    /** @var array $validEvents */
    private $validEvents = ['onActivate', 'onDeactivate'];

    /**
     * There is another service for syntax validation and we won't validate syntax in this method.
     *
     * @param ModuleSetting $moduleSetting
     * @param string        $moduleId
     * @param int           $shopId
     */
    public function validate(ModuleSetting $moduleSetting, string $moduleId, int $shopId)
    {
        if (!$this->canValidate($moduleSetting)) {
            throw new ModuleSetupValidationException('Setting ' . $moduleSetting->getName() . ' can not be validated by this class.');
        }

        $events = $moduleSetting->getValue();
        foreach ($this->validEvents as $validEventName) {
            if (is_array($events) && array_key_exists($validEventName, $events)) {
                $this->checkIfMethodIsCallable($events[$validEventName]);
            }
        }
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @return bool
     */
    public function canValidate(ModuleSetting $moduleSetting): bool
    {
        return $moduleSetting->getName() === ModuleSetting::EVENTS;
    }

    /**
     * @param string $method
     *
     * @throws ModuleSettingNotValidException
     */
    private function checkIfMethodIsCallable(string $method)
    {
        if (!is_callable($method)) {
            throw new ModuleSettingNotValidException('The method ' . $method . ' is not callable.');
        }
    }
}
