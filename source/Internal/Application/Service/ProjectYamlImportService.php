<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Service;

use OxidEsales\EshopCommunity\Internal\Application\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Application\Service\ProjectYamlImportServiceInterface;

/**
 * Class ProjectYamlImportService
 */
class ProjectYamlImportService implements ProjectYamlImportServiceInterface
{

    /**
     * @var ProjectYamlDaoInterface
     */
    private $projectYamlDao;

    /**
     * ProjectYamlImportService constructor.
     *
     * @param ProjectYamlDaoInterface $dao
     */
    public function __construct(ProjectYamlDaoInterface $dao)
    {
        $this->projectYamlDao = $dao;
    }

    /**
     * @param string $serviceDir
     *
     */
    public function addImport(string $serviceDir)
    {
        $projectConfig = $this->projectYamlDao->loadProjectConfigFile();

        $projectConfig->addImport($serviceDir . DIRECTORY_SEPARATOR . 'services.yaml');

        $this->projectYamlDao->saveProjectConfigFile($projectConfig);
    }

    /**
     * @param string $serviceDir
     */
    public function removeImport(string $serviceDir)
    {
        $projectConfig = $this->projectYamlDao->loadProjectConfigFile();

        $projectConfig->removeImport($serviceDir . DIRECTORY_SEPARATOR . 'services.yaml');

        $this->projectYamlDao->saveProjectConfigFile($projectConfig);
    }

    /**
     * Checks if the import files exist and if not removes them
     */
    public function removeNonExistingImports()
    {
        $projectConfig = $this->projectYamlDao->loadProjectConfigFile();

        $configChanged = false;
        foreach ($projectConfig->getImportFileNames() as $fileName) {
            if (file_exists($fileName)) {
                continue;
            }
            $projectConfig->removeImport($fileName);
            $configChanged = true;
        }

        if ($configChanged) {
            $this->projectYamlDao->saveProjectConfigFile($projectConfig);
        }
    }
}
