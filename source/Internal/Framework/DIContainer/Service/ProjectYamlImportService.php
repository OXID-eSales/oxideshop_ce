<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ProjectYamlDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Exception\NoServiceYamlException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
class ProjectYamlImportService implements ProjectYamlImportServiceInterface
{
    const SERVICE_FILE_NAME = 'services.yaml';

    /**
     * @var ProjectYamlDaoInterface
     */
    private $projectYamlDao;

    /**
     * @var BasicContextInterface
     */
    private $context;

    public function __construct(ProjectYamlDaoInterface $projectYamlDao, BasicContextInterface $context)
    {
        $this->projectYamlDao = $projectYamlDao;
        $this->context = $context;
    }

    /**
     * @param string $serviceDir
     */
    public function addImport(string $serviceDir)
    {
        if (!realpath($serviceDir)) {
            throw new NoServiceYamlException();
        }
        $projectConfig = $this->projectYamlDao->loadProjectConfigFile();
        $projectConfig->addImport($this->getServiceRelativeFilePath($serviceDir));

        $this->projectYamlDao->saveProjectConfigFile($projectConfig);
    }

    /**
     * @param string $serviceDir
     */
    public function removeImport(string $serviceDir)
    {
        $projectConfig = $this->projectYamlDao->loadProjectConfigFile();

        $projectConfig->removeImport($this->getServiceRelativeFilePath($serviceDir));

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
            if (file_exists($this->getAbsolutePath($fileName))) {
                continue;
            }
            $projectConfig->removeImport($fileName);
            $configChanged = true;
        }

        if ($configChanged) {
            $this->projectYamlDao->saveProjectConfigFile($projectConfig);
        }
    }

    /**
     * @param $fileName
     * @return string
     */
    private function getAbsolutePath($fileName): string
    {
        $fileAbsolutePath = Path::makeAbsolute(
            $fileName,
            Path::getDirectory($this->context->getGeneratedServicesFilePath())
        );
        return $fileAbsolutePath;
    }

    /**
     * @param string $serviceDir
     * @return string
     */
    private function getServiceRelativeFilePath(string $serviceDir): string
    {
        return Path::makeRelative(
            $serviceDir . DIRECTORY_SEPARATOR . static::SERVICE_FILE_NAME,
            Path::getDirectory($this->context->getGeneratedServicesFilePath())
        );
    }
}
