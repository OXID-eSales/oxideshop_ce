<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Dao;

use OxidEsales\EshopCommunity\Internal\ProjectDIConfig\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 */
class ProjectYamlDao implements ProjectYamlDaoInterface
{
   /**
     * @var ContextInterface $context
     */
    private $context;

    /**
     * ProjectYamlDao constructor.
     *
     * @param ContextInterface $context
     */
    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return DIConfigWrapper
     */
    public function loadProjectConfigFile(): DIConfigWrapper
    {
        return $this->loadDIConfigFile($this->getProjectFileName());
    }

    /**
     * @param DIConfigWrapper $config
     */
    public function saveProjectConfigFile(DIConfigWrapper $config)
    {
        file_put_contents($this->getProjectFileName(), Yaml::dump($config->getConfigAsArray(), 3, 2));
        if (file_exists($this->context->getContainerCacheFile())) {
            unlink($this->context->getContainerCacheFile());
        }
    }

    /**
     * @param string $path
     *
     * @return array
     */
    public function loadDIConfigFile(string $path): DIConfigWrapper
    {
        $yamlArray = null;
        if (file_exists($path)) {
            $yamlArray = Yaml::parse(file_get_contents($path));
        }
        if (is_null($yamlArray)) {
            $yamlArray = [];
        }
        return new DIConfigWrapper($yamlArray);
    }

    /**
     * @return string
     */
    private function getProjectFileName(): string
    {
        return $this->context->getShopDir() . DIRECTORY_SEPARATOR . ProjectYamlDaoInterface::PROJECT_FILE_NAME;
    }
}
