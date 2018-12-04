<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Service;

use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIServiceWrapper;
use OxidEsales\EshopCommunity\Internal\Application\Exception\NoServiceYamlException;

/**
 * @internal
 */
class ShopActivationService implements ShopActivationServiceInterface
{

    /**
     * @var ProjectYamlDaoInterface $dao
     */
    public $dao;

    /**
     * ShopAwareServiceActivationService constructor.
     *
     * @param ProjectYamlDaoInterface $dao
     */
    public function __construct(ProjectYamlDaoInterface $dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param string $moduleDir
     * @param array  $shopIds
     * @return void
     */
    public function activateServicesForShops(string $moduleDir, array $shopIds)
    {
        $moduleConfigFile = $moduleDir . DIRECTORY_SEPARATOR . 'services.yaml';
        try {
            $moduleConfig = $this->getModuleConfig($moduleConfigFile);
        } catch (NoServiceYamlException $e) {
            return;
        }

        $projectConfig = $this->dao->loadProjectConfigFile();
        $projectConfig->addImport($moduleConfigFile);

        /** @var DIServiceWrapper $service */
        foreach ($moduleConfig->getServices() as $service) {
            if (!$service->isShopAware()) {
                continue;
            }
            if ($projectConfig->hasService($service->getKey())) {
                $service = $projectConfig->getService($service->getKey());
            }
            $service->addActiveShops($shopIds);
            $projectConfig->addOrUpdateService($service);
        }

        $this->dao->saveProjectConfigFile($projectConfig);
    }


    /**
     * @param string $moduleDir
     * @param array  $shopIds
     * @return void
     */
    public function deactivateServicesForShops(string $moduleDir, array $shopIds)
    {
        $moduleConfigFile = $moduleDir . DIRECTORY_SEPARATOR . 'services.yaml';
        try {
            $moduleConfig = $this->getModuleConfig($moduleConfigFile);
        } catch (NoServiceYamlException $e) {
            return;
        }
        $projectConfig = $this->dao->loadProjectConfigFile();

        /** @var DIServiceWrapper $service */
        foreach ($moduleConfig->getServices() as $service) {
            if (!$service->isShopAware()) {
                continue;
            }
            $service = $projectConfig->getService($service->getKey());
            $service->removeActiveShops($shopIds);
            $projectConfig->addOrUpdateService($service);
        }
        $this->dao->saveProjectConfigFile($projectConfig);
    }

    /**
     * @param string $moduleConfigFile
     *
     * @return DIConfigWrapper
     * @throws NoServiceYamlException
     */
    private function getModuleConfig(string $moduleConfigFile): DIConfigWrapper
    {
        if (!file_exists($moduleConfigFile)) {
            throw new NoServiceYamlException();
        }
        return $this->dao->loadDIConfigFile($moduleConfigFile);
    }
}
