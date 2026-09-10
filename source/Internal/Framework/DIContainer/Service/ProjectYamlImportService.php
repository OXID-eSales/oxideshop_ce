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
use Symfony\Component\Filesystem\Path;

/**
 * @internal
 */
class ProjectYamlImportService implements ProjectYamlImportServiceInterface
{
    public function __construct(
        private ProjectYamlDaoInterface $projectYamlDao,
        private BasicContextInterface $context
    ) {
    }

    public function addImportFromFilePath(string $serviceFilePath): void
    {
        if (!is_file($serviceFilePath)) {
            throw new NoServiceYamlException();
        }
        $projectConfig = $this->projectYamlDao->loadProjectConfigFile();
        $projectConfig->addImport($this->getRelativeFilePath($serviceFilePath));

        $this->projectYamlDao->saveProjectConfigFile($projectConfig);
    }

    public function removeImportFromFilePath(string $serviceFilePath): void
    {
        $projectConfig = $this->projectYamlDao->loadProjectConfigFile();

        $projectConfig->removeImport($this->getRelativeFilePath($serviceFilePath));

        $this->projectYamlDao->saveProjectConfigFile($projectConfig);
    }

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

    private function getAbsolutePath($fileName): string
    {
        return Path::makeAbsolute(
            $fileName,
            Path::getDirectory($this->context->getGeneratedServicesFilePath())
        );
    }

    private function getRelativeFilePath(string $serviceFilePath): string
    {
        return Path::makeRelative(
            $serviceFilePath,
            Path::getDirectory($this->context->getGeneratedServicesFilePath())
        );
    }
}
