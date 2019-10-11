<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event\SettingChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ModuleSettingBridge implements ModuleSettingBridgeInterface
{
    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var SettingDaoInterface
     */
    private $settingDao;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        ContextInterface $context,
        ModuleConfigurationDaoInterface $moduleConfigurationDao,
        SettingDaoInterface $settingDao,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->context = $context;
        $this->moduleConfigurationDao = $moduleConfigurationDao;
        $this->settingDao = $settingDao;
        $this->eventDispatcher = $eventDispatcher;
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
        $this->settingDao->save($setting, $moduleId, $this->context->getCurrentShopId());
        $this->moduleConfigurationDao->save($moduleConfiguration, $this->context->getCurrentShopId());

        $this->eventDispatcher->dispatch(
            SettingChangedEvent::NAME,
            new SettingChangedEvent(
                $name,
                $this->context->getCurrentShopId(),
                $moduleId
            )
        );
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
