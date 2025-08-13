<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ModuleConfigurationExportDataMapper implements ModuleConfigurationExportDataMapperInterface
{
    private array $dataMappers;

    public function __construct(
        private ModuleStateServiceInterface $moduleStateService,
        private ContextInterface $context,
        ModuleConfigurationExportDataMapperInterface ...$dataMappers
    ) {
        $this->dataMappers = $dataMappers;
    }

    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [
            'id' => $configuration->getId(),
            'title' => $this->getPreferredTranslation($configuration->getTitle()),
            'description' => $this->getPreferredTranslation($configuration->getDescription()),
            'version' => $configuration->getVersion(),
            'author' => $configuration->getAuthor(),
            'url' => $configuration->getUrl(),
            'email' => $configuration->getEmail(),
        ];

        if ($this->moduleStateService->isActive($configuration->getId(), $this->context->getCurrentShopId())) {
            $data['activeInShops'] = ['activeInShop' => [$this->context->getFacts()->getShopUrl()]];
        } else {
            $data['activeInShops'] = ['activeInShop' => []];
        }

        foreach ($this->dataMappers as $dataMapper) {
            $data = array_merge($data, $dataMapper->toData($configuration));
        }

        return $data;
    }

    private function getPreferredTranslation(array $title): string
    {
        if (empty($title)) {
            return '';
        }

        return $title['en'] ?? array_values($title)[0];
    }
}
