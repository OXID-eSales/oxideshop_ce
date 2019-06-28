<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Validator;

use function is_array;

use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Adapter\Configuration\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Common\Exception\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ControllersDuplicationModuleConfigurationException;

/**
 * @internal
 */
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
        if ($configuration->hasSetting(ModuleSetting::CONTROLLERS)) {
            $moduleSetting = $configuration->getSetting(ModuleSetting::CONTROLLERS);

            $shopControllerClassMap = $this->shopAdapter->getShopControllerClassMap();

            $controllerClassMap = array_merge(
                $shopControllerClassMap,
                $this->getModulesControllerClassMap($shopId)
            );

            $this->validateForControllerKeyDuplication($moduleSetting, $controllerClassMap);
            $this->validateForControllerNamespaceDuplication($moduleSetting, $controllerClassMap);
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
     * @param ModuleSetting $moduleSetting
     * @param array         $controllerClassMap
     *
     * @throws ControllersDuplicationModuleConfigurationException
     */
    private function validateForControllerNamespaceDuplication(ModuleSetting $moduleSetting, array $controllerClassMap)
    {
        $duplications = array_intersect(
            $moduleSetting->getValue(),
            $controllerClassMap
        );

        if (!empty($duplications)) {
            throw new ControllersDuplicationModuleConfigurationException(
                'Controller namespaces duplication: ' . implode(', ', $duplications)
            );
        }
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @param array         $controllerClassMap
     *
     * @throws ControllersDuplicationModuleConfigurationException
     */
    private function validateForControllerKeyDuplication(ModuleSetting $moduleSetting, array $controllerClassMap)
    {
        $duplications = array_intersect_key(
            $this->arrayKeysToLowerCase($moduleSetting->getValue()),
            $controllerClassMap
        );

        if (!empty($duplications)) {
            throw new ControllersDuplicationModuleConfigurationException(
                'Controller keys duplication: ' . implode(', ', $duplications)
            );
        }
    }

    /**
     * @param array $array
     * @return array
     */
    private function arrayKeysToLowerCase(array $array): array
    {
        return array_change_key_case($array, CASE_LOWER);
    }
}
