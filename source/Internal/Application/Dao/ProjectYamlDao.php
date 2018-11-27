<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Dao;

use OxidEsales\EshopCommunity\Internal\Application\Events\ProjectYamlChangedEvent;
use OxidEsales\EshopCommunity\Internal\Application\DataObject\DIConfigWrapper;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 */
class ProjectYamlDao implements ProjectYamlDaoInterface
{

    const PROJECT_FILE_NAME = 'project.yaml';

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
    }

    /**
     * @param string $path
     *
     * @return array
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
    private function getProjectFileName(): string
    {
        return $this->context->getShopDir() . DIRECTORY_SEPARATOR . self::PROJECT_FILE_NAME;
    }
}
