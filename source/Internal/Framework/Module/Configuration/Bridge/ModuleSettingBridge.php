<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event\SettingChangedEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @deprecated use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface
 */
class ModuleSettingBridge implements ModuleSettingBridgeInterface
{
    public function __construct(
        private ContextInterface $context,
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param string $moduleId
     */
    public function save(string $name, $value, string $moduleId): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $this->context->getCurrentShopId());
        $setting = $moduleConfiguration->getModuleSetting($name);
        $setting->setValue($value);
        $this->moduleConfigurationDao->save($moduleConfiguration, $this->context->getCurrentShopId());

        $this->eventDispatcher->dispatch(new SettingChangedEvent($name, $this->context->getCurrentShopId(), $moduleId));
    }

    /**
     * @param string $name
     * @param string $moduleId
     * @return mixed
     */
    public function get(string $name, string $moduleId)
    {
        $configuration = $this->moduleConfigurationDao->get($moduleId, $this->context->getCurrentShopId());
        return $configuration->getModuleSetting($name)->getValue();
    }
}
