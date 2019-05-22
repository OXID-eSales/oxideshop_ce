<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Validator;

use function is_array;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException;

/**
 * @internal
 */
class EventsModuleSettingValidator implements ModuleSettingValidatorInterface
{
    /** @var array $validEvents */
    private $validEvents = ['onActivate', 'onDeactivate'];
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @param ShopAdapterInterface $shopAdapter
     */
    public function __construct(ShopAdapterInterface $shopAdapter)
    {
        $this->shopAdapter = $shopAdapter;
    }

    /**
     * There is another service for syntax validation and we won't validate syntax in this method.
     *
     * @param ModuleSetting $moduleSetting
     * @param string        $moduleId
     * @param int           $shopId
     *
     * @throws WrongModuleSettingException
     */
    public function validate(ModuleSetting $moduleSetting, string $moduleId, int $shopId)
    {
        if (!$this->canValidate($moduleSetting)) {
            throw new WrongModuleSettingException($moduleSetting, self::class);
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
        $this->isNamespacedClass($method);
        if (!is_callable($method) && $this->isNamespacedClass($method)) {
            throw new ModuleSettingNotValidException('The method ' . $method . ' is not callable.');
        }
    }

    /**
     * This is needed only for the modules which has non namespaced classes.
     * This method MUST be removed when support for non namespaced modules will be dropped (metadata v1.*).
     *
     * @param string $method
     * @return bool
     */
    private function isNamespacedClass(string $method): bool
    {
        $className = explode('::', $method)[0];
        if ($this->shopAdapter->isNamespace($className)) {
            return true;
        }
        return false;
    }
}
