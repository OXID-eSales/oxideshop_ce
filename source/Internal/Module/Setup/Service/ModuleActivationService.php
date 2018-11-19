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
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Provider\ModuleConfigurationProviderInterface;
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
     * @var ModuleConfigurationProviderInterface
     */
    private $moduleConfigurationProvider;

    /**
     * @var ModuleCacheServiceInterface
     */
    private $moduleCacheService;

    /**
     * @var array
     */
    private $moduleSettingHandlers = [];

    /**
     * @var array
     */
    private $moduleSettingValidators = [];

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * ModuleActivationService constructor.
     *
     * @param ModuleConfigurationProviderInterface $moduleConfigurationProvider
     * @param EventDispatcherInterface             $eventDispatcher
     * @param ModuleCacheServiceInterface          $moduleCacheService
     */
    public function __construct(
        ModuleConfigurationProviderInterface $moduleConfigurationProvider,
        EventDispatcherInterface             $eventDispatcher,
        ModuleCacheServiceInterface          $moduleCacheService
    ) {
        $this->moduleConfigurationProvider = $moduleConfigurationProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->moduleCacheService = $moduleCacheService;
    }


    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function activate(string $moduleId, int $shopId)
    {
        $environmentName = 'dev';
        $moduleConfiguration = $this->moduleConfigurationProvider->getModuleConfiguration(
            $moduleId,
            $environmentName,
            $shopId
        );

        $this->validateModuleSettings($moduleConfiguration, $moduleId, $shopId);

        /**
         * @todo [II] wrap it in transaction.
         */
        $this->handleModuleSettingsOnActivation($moduleConfiguration, $moduleId, $shopId);

        $this->eventDispatcher->dispatch(
            AfterModuleActivationEvent::NAME,
            new AfterModuleActivationEvent($environmentName, $shopId, $moduleId)
        );
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     */
    public function deactivate(string $moduleId, int $shopId)
    {
        $environmentName = 'dev';

        $moduleConfiguration = $this
            ->moduleConfigurationProvider
            ->getModuleConfiguration($moduleId, $environmentName, $shopId);

        $this->eventDispatcher->dispatch(
            BeforeModuleDeactivationEvent::NAME,
            new BeforeModuleDeactivationEvent($environmentName, $shopId, $moduleId)
        );

        foreach ($moduleConfiguration->getSettings() as $setting) {
            /** @var ModuleSettingHandlerInterface $handler */
            $handler = $this->getHandler($setting);
            $handler->handleOnModuleDeactivation($setting, $moduleId, $shopId);
        }

        //$this->moduleCacheService->invalidateModuleCache($moduleId, $shopId);
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
     * @param string              $moduleId
     * @param int                 $shopId
     */
    private function handleModuleSettingsOnActivation(ModuleConfiguration $moduleConfiguration, string $moduleId, int $shopId)
    {
        foreach ($moduleConfiguration->getSettings() as $setting) {
            $handler = $this->getHandler($setting);
            $handler->handleOnModuleActivation($setting, $moduleId, $shopId);
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
     * @param string              $moduleId
     * @param int                 $shopId
     */
    private function validateModuleSettings(ModuleConfiguration $moduleConfiguration, string $moduleId, int $shopId)
    {
        foreach ($moduleConfiguration->getSettings() as $setting) {
            foreach ($this->moduleSettingValidators as $validator) {
                if ($validator->canValidate($setting)) {
                    $validator->validate($setting, $moduleId, $shopId);
                }
            }
        }
    }
}
