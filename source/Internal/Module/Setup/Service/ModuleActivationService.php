<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\AfterModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingHandlerNotFoundException;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ModuleSettingHandlerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ModuleSettingValidatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
class ModuleActivationService implements ModuleActivationServiceInterface
{
    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $ModuleConfigurationDao;

    /**
     * @var ModuleCacheServiceInterface
     */
    private $moduleCacheService;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $moduleSettingHandlers = [];

    /**
     * @var array
     */
    private $moduleSettingValidators = [];

    /**
     * ModuleActivationService constructor.
     *
     * @param ModuleConfigurationDaoInterface $ModuleConfigurationDao
     * @param EventDispatcherInterface        $eventDispatcher
     * @param ModuleCacheServiceInterface     $moduleCacheService
     */
    public function __construct(
        ModuleConfigurationDaoInterface $ModuleConfigurationDao,
        EventDispatcherInterface        $eventDispatcher,
        ModuleCacheServiceInterface     $moduleCacheService
    ) {
        $this->ModuleConfigurationDao = $ModuleConfigurationDao;
        $this->eventDispatcher = $eventDispatcher;
        $this->moduleCacheService = $moduleCacheService;
        //updateChain
        //handle module yml services / ShopActivationService
        // ACTIVE_MODULES: add to, delete from
        //autoActivate - projectConfigurationDao
        // State service
        // transaction service
    }


    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $moduleConfiguration = $this->ModuleConfigurationDao->get($moduleId, $shopId);

        $this->validateModuleSettings($moduleConfiguration, $shopId);

        /**
         * @todo [II] wrap it in transaction.
         */
        $this->handleModuleSettingsOnActivation($moduleConfiguration, $shopId);

        $this->eventDispatcher->dispatch(
            AfterModuleActivationEvent::NAME,
            new AfterModuleActivationEvent($shopId, $moduleId)
        );

        $this->moduleCacheService->invalidateModuleCache($moduleId, $shopId);
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        $this->eventDispatcher->dispatch(
            BeforeModuleDeactivationEvent::NAME,
            new BeforeModuleDeactivationEvent($shopId, $moduleId)
        );

        $moduleConfiguration = $this
            ->ModuleConfigurationDao
            ->get($moduleId, $shopId);

        foreach ($moduleConfiguration->getSettings() as $setting) {
            /** @var ModuleSettingHandlerInterface $handler */
            $handler = $this->getHandler($setting);
            $handler->handleOnModuleDeactivation($setting, $moduleId, $shopId);
        }

        $this->moduleCacheService->invalidateModuleCache($moduleId, $shopId);
    }

    /**
     * @param ModuleSettingHandlerInterface $moduleSettingHandler
     */
    public function addHandler(ModuleSettingHandlerInterface $moduleSettingHandler)
    {
        $this->moduleSettingHandlers[] = $moduleSettingHandler;
    }

    /**
     * @param ModuleSettingValidatorInterface $moduleSettingValidator
     */
    public function addValidator(ModuleSettingValidatorInterface $moduleSettingValidator)
    {
        $this->moduleSettingValidators[] = $moduleSettingValidator;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    private function handleModuleSettingsOnActivation(ModuleConfiguration $moduleConfiguration, int $shopId)
    {
        foreach ($moduleConfiguration->getSettings() as $setting) {
            $handler = $this->getHandler($setting);
            $handler->handleOnModuleActivation($setting, $moduleConfiguration->getId(), $shopId);
        }
    }

    /**
     * @param ModuleSetting $setting
     * @return ModuleSettingHandlerInterface
     * @throws ModuleSettingHandlerNotFoundException
     */
    private function getHandler(ModuleSetting $setting): ModuleSettingHandlerInterface
    {
        foreach ($this->moduleSettingHandlers as $moduleSettingHandler) {
            /** @var ModuleSettingHandlerInterface $moduleSettingHandler */
            if ($moduleSettingHandler->canHandle($setting)) {
                return $moduleSettingHandler;
            }
        }

        throw new ModuleSettingHandlerNotFoundException(
            'Handler for the setting with name "' . $setting->getName() . '" wasn\'t found.'
        );
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    private function validateModuleSettings(ModuleConfiguration $moduleConfiguration, int $shopId)
    {
        foreach ($moduleConfiguration->getSettings() as $setting) {
            foreach ($this->moduleSettingValidators as $validator) {
                if ($validator->canValidate($setting)) {
                    $validator->validate($setting, $moduleConfiguration->getId(), $shopId);
                }
            }
        }
    }
}
