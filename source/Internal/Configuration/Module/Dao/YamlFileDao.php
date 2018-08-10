<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Dao;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;
use SplFileObject;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFileDao
 */
class YamlFileDao implements ProjectConfigurationDaoInterface
{
    /**
     * @var SplFileObject
     */
    private $file;

    /**
     * @var ProjectConfiguration
     */
    private $projectConfiguration;

    /**
     * JsonFileStorage constructor.
     *
     * @param SplFileObject        $file
     * @param ProjectConfiguration $projectConfiguration
     */
    public function __construct(SplFileObject $file, ProjectConfiguration $projectConfiguration)
    {
        $this->file = $file;
        $this->projectConfiguration = $projectConfiguration;
    }

    /**
     * @return ProjectConfiguration
     */
    public function getConfiguration(): ProjectConfiguration
    {
        $data = $this->getConfigurationData();

        return new ProjectConfiguration();
    }

    /**
     * @param ProjectConfiguration $configuration
     *
     * @throws \Exception
     */
    public function persistConfiguration(ProjectConfiguration $configuration)
    {
        $data = [];
        $this->persistConfigurationData($data);
    }

    /**
     * @return array
     */
    private function getConfigurationData(): array
    {
        $yaml = $this->getFileContent();

        return Yaml::parse($yaml);
    }

    /**
     * @return string
     */
    private function getFileContent(): string
    {
        $this->file->rewind();

        $string = '';
        while (!$this->file->eof()) {
            $string .= $this->file->fgets();
        }

        return $string;
    }

    /**
     * @param array $data
     *
     * @throws \Exception
     */
    private function persistConfigurationData(array $data)
    {
        $string = Yaml::dump($data);
        $fileLockAcquired = $this->file->flock(LOCK_EX | LOCK_NB, $wouldBlock) && !$wouldBlock;
        if (!$fileLockAcquired) {
            throw new \Exception('Could not acquire file lock');
        }

        $this->file->ftruncate(0);
        $this->file->fwrite($string);
        $this->file->rewind();
        $this->file->flock(LOCK_UN);
    }
}
