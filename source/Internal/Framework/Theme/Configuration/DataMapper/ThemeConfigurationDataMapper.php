<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

readonly class ThemeConfigurationDataMapper implements ThemeConfigurationDataMapperInterface
{
    /**
     * @var ThemeConfigurationDataMapperInterface[]
     */
    private array $dataMappers;

    public function __construct(ThemeConfigurationDataMapperInterface ...$dataMappers)
    {
        $this->dataMappers = $dataMappers;
    }

    public function toData(ThemeConfiguration $configuration): array
    {
        // Only mutable state is persisted per shop/environment. Static metadata
        // (version, title, description, thumbnail, author, parentTheme) and the
        // setting definitions (type, group, constraints) live in the theme's
        // theme.yaml and are merged in on read by the DAO.
        $data = [
            'id' => $configuration->getId(),
            'themeSource' => $configuration->getThemeSource(),
            'activated' => $configuration->isActivated(),
        ];

        foreach ($this->dataMappers as $dataMapper) {
            $data = array_merge($data, $dataMapper->toData($configuration));
        }

        return $data;
    }

    public function fromData(ThemeConfiguration $themeConfiguration, array $data): ThemeConfiguration
    {
        $themeConfiguration
            ->setId($data['id'])
            ->setThemeSource($data['themeSource'])
            ->setVersion($data['version'])
            ->setActivated((bool) $data['activated']);

        if (isset($data['parentTheme'])) {
            $themeConfiguration->setParentTheme($data['parentTheme']);
        }

        if (isset($data['title'])) {
            $themeConfiguration->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $themeConfiguration->setDescription($data['description']);
        }

        if (isset($data['thumbnail'])) {
            $themeConfiguration->setThumbnail($data['thumbnail']);
        }

        if (isset($data['author'])) {
            $themeConfiguration->setAuthor($data['author']);
        }

        foreach ($this->dataMappers as $dataMapper) {
            $themeConfiguration = $dataMapper->fromData($themeConfiguration, $data);
        }

        return $themeConfiguration;
    }
}
