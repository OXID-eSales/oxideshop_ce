<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

class ProjectYamlDao implements ProjectYamlDaoInterface
{
    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * ProjectYamlDao constructor.
     */
    public function __construct(BasicContextInterface $context, Filesystem $filesystem)
    {
        $this->context = $context;
        $this->filesystem = $filesystem;
    }

    public function loadProjectConfigFile(): DIConfigWrapper
    {
        return $this->loadDIConfigFile($this->context->getGeneratedServicesFilePath());
    }

    public function saveProjectConfigFile(DIConfigWrapper $config): void
    {
        $config = $this->convertAbsolutePathsToRelative($config);

        if (!$this->filesystem->exists($this->getGeneratedServicesFileDirectory())) {
            $this->filesystem->mkdir($this->getGeneratedServicesFileDirectory());
        }

        file_put_contents(
            $this->context->getGeneratedServicesFilePath(),
            Yaml::dump($config->getConfigAsArray(), 3, 2)
        );
    }

    public function loadDIConfigFile(string $path): DIConfigWrapper
    {
        $yamlArray = [];

        if (file_exists($path)) {
            $yamlArray = Yaml::parse(file_get_contents($path), Yaml::PARSE_CUSTOM_TAGS) ?? [];
        }

        return new DIConfigWrapper($yamlArray);
    }

    private function getGeneratedServicesFileDirectory(): string
    {
        return \dirname($this->context->getGeneratedServicesFilePath());
    }

    private function convertAbsolutePathsToRelative(DIConfigWrapper $configWrapper): DIConfigWrapper
    {
        foreach ($configWrapper->getImportFileNames() as $fileName) {
            if (Path::isAbsolute($fileName)) {
                $relativePath = Path::makeRelative(
                    $fileName,
                    Path::getDirectory($this->context->getGeneratedServicesFilePath())
                );
                $configWrapper->addImport($relativePath);
                $configWrapper->removeImport($fileName);
            }
        }

        return $configWrapper;
    }
}
