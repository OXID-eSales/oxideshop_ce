<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use function is_array;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSettingNotValidException;

class EventsValidator implements ModuleConfigurationValidatorInterface
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
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     *
     * @throws ModuleSettingNotValidException
     */
    public function validate(ModuleConfiguration $configuration, int $shopId)
    {
        if ($configuration->hasEvents()) {
            $events = [];

            foreach ($configuration->getEvents() as $event) {
                $events[$event->getAction()] = $event->getMethod();
            }
            foreach ($this->validEvents as $validEventName) {
                if (is_array($events) && array_key_exists($validEventName, $events)) {
                    $this->checkIfMethodIsCallable($events[$validEventName]);
                }
            }
        }
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
