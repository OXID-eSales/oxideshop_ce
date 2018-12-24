<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIServiceWrapper;
use OxidEsales\EshopCommunity\Internal\Application\Exception\NoServiceYamlException;

/**
 * @internal
 */
class ModuleServicesActivationService implements ModuleServicesActivationServiceInterface
{

    /**
     * @var ProjectYamlDaoInterface $dao
     */
    private $dao;

    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * ModuleServicesActivationService constructor.
     * @param ProjectYamlDaoInterface $dao
     * @param ShopAdapterInterface    $shopAdapter
     */
    public function __construct(ProjectYamlDaoInterface $dao, ShopAdapterInterface $shopAdapter)
    {
        $this->dao = $dao;
        $this->shopAdapter = $shopAdapter;
    }

    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return void
     * @throws \OxidEsales\EshopCommunity\Internal\Application\Exception\MissingServiceException
     */
    public function activateModuleServices(string $moduleId, int $shopId)
    {
        $moduleConfigFile = $this->getModuleServicesFilePath($moduleId);
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
            $service->addActiveShops([$shopId]);
            $projectConfig->addOrUpdateService($service);
        }

        $this->dao->saveProjectConfigFile($projectConfig);
    }


    /**
     * @param string $moduleId
     * @param int    $shopId
     * @return void
     * @throws \OxidEsales\EshopCommunity\Internal\Application\Exception\MissingServiceException
     */
    public function deactivateModuleServices(string $moduleId, int $shopId)
    {
        $moduleConfigFile = $this->getModuleServicesFilePath($moduleId);
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
            $service->removeActiveShops([$shopId]);
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

    /**
     * @param string $moduleId
     * @return string
     */
    private function getModuleServicesFilePath(string $moduleId): string
    {
        return $this->shopAdapter->getModuleFullPath($moduleId) . DIRECTORY_SEPARATOR . 'services.yaml';
    }
}
