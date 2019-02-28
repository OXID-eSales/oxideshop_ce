<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Dao;

use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 */
class ProjectYamlDao implements ProjectYamlDaoInterface
{
    /**
     * @var BasicContextInterface $context
     */
    private $context;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * ProjectYamlDao constructor.
     * @param BasicContextInterface $context
     * @param Filesystem            $filesystem
     */
    public function __construct(BasicContextInterface $context, Filesystem $filesystem)
    {
        $this->context = $context;
        $this->filesystem = $filesystem;
    }

    /**
     * @return DIConfigWrapper
     */
    public function loadProjectConfigFile(): DIConfigWrapper
    {
        return $this->loadDIConfigFile($this->context->getGeneratedServicesFilePath());
    }

    /**
     * @param DIConfigWrapper $config
     */
    public function saveProjectConfigFile(DIConfigWrapper $config)
    {
        if (!$this->filesystem->exists($this->getGeneratedServicesFileDirectory())) {
            $this->filesystem->mkdir($this->getGeneratedServicesFileDirectory());
        }

        file_put_contents($this->context->getGeneratedServicesFilePath(), Yaml::dump($config->getConfigAsArray(), 3, 2));
    }

    /**
     * @param string $path
     *
     * @return DIConfigWrapper
     */
    public function loadDIConfigFile(string $path): DIConfigWrapper
    {
        $yamlArray = [];

        if (file_exists($path)) {
            $yamlArray = Yaml::parse(file_get_contents($path)) ?? [];
        }

        return new DIConfigWrapper($yamlArray);
    }

    /**
     * @return string
     */
    private function getGeneratedServicesFileDirectory(): string
    {
        return \dirname($this->context->getGeneratedServicesFilePath());
    }
}
