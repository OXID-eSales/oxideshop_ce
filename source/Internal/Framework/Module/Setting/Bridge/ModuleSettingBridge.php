<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Event\SettingChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Helper\ModuleIdPreparatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\SettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
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

    /**
     * @var ModuleIdPreparatorInterface
     */
    private $moduleIdPreparator;

    public function __construct(
        ContextInterface $context,
        ModuleConfigurationDaoInterface $moduleConfigurationDao,
        SettingDaoInterface $settingDao,
        EventDispatcherInterface $eventDispatcher,
        ModuleIdPreparatorInterface $moduleIdPreparator
    ) {
        $this->context = $context;
        $this->moduleConfigurationDao = $moduleConfigurationDao;
        $this->settingDao = $settingDao;
        $this->eventDispatcher = $eventDispatcher;
        $this->moduleIdPreparator = $moduleIdPreparator;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param string $moduleId
     */
    public function save(string $name, $value, string $moduleId): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $this->context->getCurrentShopId());

        if (!empty($moduleConfiguration->getModuleSettings())) {
            $moduleSettings = $moduleConfiguration->getModuleSettings();
            foreach ($moduleSettings as $key => $moduleSetting) {
                if ($moduleSetting->getName() === $name) {
                    $moduleSetting->setValue($value);
                    $this->settingDao->save($moduleSetting, $moduleId, $this->context->getCurrentShopId());
                }
            }
            $this->moduleConfigurationDao->save($moduleConfiguration, $this->context->getCurrentShopId());
            $this->eventDispatcher->dispatch(
                SettingChangedEvent::NAME,
                new SettingChangedEvent(
                    $name,
                    $this->context->getCurrentShopId(),
                    $this->moduleIdPreparator->prepare($moduleId)
                )
            );
        }
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
