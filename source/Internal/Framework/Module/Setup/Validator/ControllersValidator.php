<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ControllersDuplicationModuleConfigurationException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use Psr\Log\LoggerInterface;

use function is_array;
use function in_array;
use function array_key_exists;

class ControllersValidator implements ModuleConfigurationValidatorInterface
{
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @var ShopConfigurationSettingDaoInterface
     */
    private $shopConfigurationSettingDao;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ShopAdapterInterface $shopAdapter
     * @param ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
     * @param LoggerInterface $logger
     */
    public function __construct(
        ShopAdapterInterface $shopAdapter,
        ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao,
        LoggerInterface $logger
    ) {
        $this->shopAdapter = $shopAdapter;
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
        $this->logger = $logger;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     *
     * @throws ControllersDuplicationModuleConfigurationException
     */
    public function validate(ModuleConfiguration $configuration, int $shopId)
    {
        if ($configuration->hasControllers()) {
            $controllerClassMap = $this->getControllersClassMap($shopId);

            foreach ($configuration->getControllers() as $controller) {
                if (!$this->controllerAlreadyExistsInMap($controller, $controllerClassMap)) {
                    $this->validateKeyDuplication($controller, $controllerClassMap);
                    $this->validateNamespaceDuplication($controller, $controllerClassMap);
                } else {
                    /**
                     * @TODO this is a wrong place to check and log database discrepancy, not only controllers should be
                     *       checked. It should be moved to separate module data discrepancy checker outside the module
                     *       validation.
                     */
                    $this->logger->error(
                        'Module data discrepancy error: module data (controller with id '
                        . $controller->getId() . ' and namespace: '
                        . $controller->getControllerClassNameSpace() . ' ) for module '
                        . $configuration->getId() . ' was present in the database before the module activation'
                    );
                }
            }
        }
    }

    private function controllerAlreadyExistsInMap(Controller $controller, array $controllerClassMap): bool
    {
        return array_key_exists(strtolower($controller->getId()), $controllerClassMap)
            && $controllerClassMap[strtolower($controller->getId())] === $controller->getControllerClassNameSpace();
    }

    /**
     * @param int $shopId
     * @return array
     */
    private function getModulesControllerClassMap(int $shopId): array
    {
        $moduleControllersClassMap = [];

        try {
            $controllersGroupedByModule = $this
                ->shopConfigurationSettingDao
                ->get(ShopConfigurationSetting::MODULE_CONTROLLERS, $shopId);

            if (is_array($controllersGroupedByModule->getValue())) {
                foreach ($controllersGroupedByModule->getValue() as $moduleControllers) {
                    $moduleControllersClassMap = array_merge($moduleControllersClassMap, $moduleControllers);
                }
            }
        } catch (EntryDoesNotExistDaoException $exception) {
        }

        return $moduleControllersClassMap;
    }

    /**
     * @param Controller $controller
     * @param array $controllerClassMap
     * @throws ControllersDuplicationModuleConfigurationException
     */
    private function validateKeyDuplication(Controller $controller, array $controllerClassMap): void
    {
        if (array_key_exists(strtolower($controller->getId()), $controllerClassMap)) {
            throw new ControllersDuplicationModuleConfigurationException(
                'Controller key duplication: ' . $controller->getId()
            );
        }
    }

    /**
     * @param Controller $controller
     * @param array $controllerClassMap
     * @throws ControllersDuplicationModuleConfigurationException
     */
    private function validateNamespaceDuplication(Controller $controller, array $controllerClassMap): void
    {
        if (in_array($controller->getControllerClassNameSpace(), $controllerClassMap, true)) {
            throw new ControllersDuplicationModuleConfigurationException(
                'Controller namespace duplication: ' . $controller->getControllerClassNameSpace()
            );
        }
    }

    /**
     * @param int $shopId
     * @return array
     */
    private function getControllersClassMap(int $shopId): array
    {
        return array_merge(
            $this->shopAdapter->getShopControllerClassMap(),
            $this->getModulesControllerClassMap($shopId)
        );
    }
}
