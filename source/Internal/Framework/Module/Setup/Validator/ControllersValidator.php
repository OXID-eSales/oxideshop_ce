<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use function is_array;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ControllersDuplicationModuleConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;

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
     * ControllersValidator constructor.
     * @param ShopAdapterInterface                 $shopAdapter
     * @param ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
     */
    public function __construct(
        ShopAdapterInterface $shopAdapter,
        ShopConfigurationSettingDaoInterface $shopConfigurationSettingDao
    ) {
        $this->shopAdapter = $shopAdapter;
        $this->shopConfigurationSettingDao = $shopConfigurationSettingDao;
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
            $shopControllerClassMap = $this->shopAdapter->getShopControllerClassMap();

            $controllerClassMap = array_merge(
                $shopControllerClassMap,
                $this->getModulesControllerClassMap($shopId)
            );

            $this->validateForControllerKeyDuplication($configuration->getControllers(), $controllerClassMap);
            $this->validateForControllerNamespaceDuplication($configuration->getControllers(), $controllerClassMap);
        }
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
     * @param Controller[] $controllers
     * @param array $controllerClassMap
     *
     * @throws ControllersDuplicationModuleConfigurationException
     */
    private function validateForControllerNamespaceDuplication(array $controllers, array $controllerClassMap)
    {
        $duplications = $this->findDuplicateControllerClassNameSpaces(
            $controllers,
            $controllerClassMap
        );

        if (!empty($duplications)) {
            throw new ControllersDuplicationModuleConfigurationException(
                'Controller namespaces duplication: ' . implode(', ', $duplications)
            );
        }
    }

    /**
     * @param Controller[] $controllers
     * @param array $controllerClassMap
     *
     * @throws ControllersDuplicationModuleConfigurationException
     */
    private function validateForControllerKeyDuplication(array $controllers, array $controllerClassMap)
    {
        $duplications = $this->findDuplicateControllerIds(
            $controllers,
            $controllerClassMap
        );

        if (!empty($duplications)) {
            throw new ControllersDuplicationModuleConfigurationException(
                'Controller keys duplication: ' . implode(', ', $duplications)
            );
        }
    }

    /**
     * @param Controller[] $controllers
     * @param array $controllerClassMap
     *
     * @return array
     */
    private function findDuplicateControllerClassNameSpaces(array $controllers, array $controllerClassMap): array
    {
        $controllerClassNameSpaces = [];

        foreach ($controllers as $controller) {
            $controllerClassNameSpaces[] = $controller->getControllerClassNameSpace();
        }

        return array_intersect($controllerClassNameSpaces, $controllerClassMap);
    }

    /**
     * @param Controller[] $controllers
     * @param array $controllerClassMap
     *
     * @return array
     */
    private function findDuplicateControllerIds(array $controllers, array $controllerClassMap): array
    {
        $controllerIds = [];

        foreach ($controllers as $controller) {
            $controllerIds[] = strtolower($controller->getId());
        }

        return array_intersect($controllerIds, $controllerClassMap);
    }
}
