<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\AppConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class AppConfigurationDao implements AppConfigurationDaoInterface
{
    public function __construct(
        private array $envConfiguration,
        private FileStorageFactoryInterface $fileStorageFactory,
        private ContextInterface $context,
    ) {
    }

    public function get(): AppConfiguration
    {
        $fileConfiguration = $this
            ->getDatabaseConfiguration('database')
            ->get()['database'];
        /** run node->normalize() to validate, etc */
        $envConfiguration = array_filter($this->envConfiguration['database']);
        $configuration = array_merge($fileConfiguration, $envConfiguration);
        $databaseConfiguration = $this
            ->createSerializer()
            ->denormalize($configuration, DatabaseConfiguration::class);

        $appConfiguration = new AppConfiguration();
        $appConfiguration->setDatabaseConfiguration($databaseConfiguration);
        return $appConfiguration;
    }

    private function createSerializer(): SerializerInterface
    {
        $normalizers = [new ObjectNormalizer()];
        return new Serializer($normalizers, []);
    }

    private function getDatabaseConfiguration(string $filename): ArrayStorageInterface
    {
        return $this->fileStorageFactory->create(
            Path::join(
                $this->context->getConfigurationDirectoryPath(),
                'app',
                "$filename.yaml",
            )
        );
    }
}
