<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
class ModuleConfigurationDataMapper implements ModuleConfigurationDataMapperInterface
{
    /** @var ModuleConfigurationDataMapperInterface[] */
    private $dataMappers = [];

    public function __construct(ModuleConfigurationDataMapperInterface ...$dataMappers)
    {
        $this->dataMappers = $dataMappers;
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [
            'id' => $configuration->getId(),
            'path' => $configuration->getPath(),
            'version' => $configuration->getVersion(),
            'autoActive' => $configuration->isAutoActive(),
            'title' => $configuration->getTitle(),
            'description' => $configuration->getDescription(),
            'lang' => $configuration->getLang(),
            'thumbnail' => $configuration->getThumbnail(),
            'author' => $configuration->getAuthor(),
            'url' => $configuration->getUrl(),
            'email' => $configuration->getEmail()
        ];

        foreach ($this->dataMappers as $dataMapper) {
            $data = array_merge($dataMapper->toData($configuration), $data);
        }

        return $data;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $data
     *
     * @return ModuleConfiguration
     */
    public function fromData(ModuleConfiguration $moduleConfiguration, array $data): ModuleConfiguration
    {
        $moduleConfiguration
            ->setId($data['id'])
            ->setPath($data['path'])
            ->setVersion($data['version'])
            ->setAutoActive($data['autoActive'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setLang($data['lang'])
            ->setThumbnail($data['thumbnail'])
            ->setAuthor($data['author'])
            ->setUrl($data['url'])
            ->setEmail($data['email']);

        foreach ($this->dataMappers as $dataMapper) {
            $moduleConfiguration = $dataMapper->fromData($moduleConfiguration, $data);
        }

        return $moduleConfiguration;
    }
}
