<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;

class EventsValidator implements ModuleConfigurationValidatorInterface
{
    /**
     * @var array
     */
    private $validEvents = ['onActivate', 'onDeactivate'];
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    public function __construct(ShopAdapterInterface $shopAdapter)
    {
        $this->shopAdapter = $shopAdapter;
    }

    /**
     * There is another service for syntax validation and we won't validate syntax in this method.
     *
     * @throws ModuleSettingNotValidException
     */
    public function validate(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($configuration->hasEvents()) {
            $events = [];

            foreach ($configuration->getEvents() as $event) {
                $events[$event->getAction()] = $event->getMethod();
            }
            foreach ($this->validEvents as $validEventName) {
                if (\is_array($events) && \array_key_exists($validEventName, $events)) {
                    $this->checkIfMethodIsCallable($events[$validEventName]);
                }
            }
        }
    }

    /**
     * @throws ModuleSettingNotValidException
     */
    private function checkIfMethodIsCallable(string $method): void
    {
        if (!\is_callable($method)) {
            throw new ModuleSettingNotValidException('The method ' . $method . ' is not callable.');
        }
    }
}
