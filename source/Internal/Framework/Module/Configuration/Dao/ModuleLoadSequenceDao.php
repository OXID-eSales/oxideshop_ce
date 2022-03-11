<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleLoadSequence;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Config\Definition\NodeInterface;

final class ModuleLoadSequenceDao implements ModuleLoadSequenceDaoInterface
{
    private FileStorageFactoryInterface $fileStorageFactory;
    private BasicContextInterface $context;
    private NodeInterface $node;

    public function __construct(
        FileStorageFactoryInterface $fileStorageFactory,
        BasicContextInterface $context,
        NodeInterface $node
    ) {
        $this->fileStorageFactory = $fileStorageFactory;
        $this->context = $context;
        $this->node = $node;
    }

    /** @inheritDoc */
    public function get(int $shopId): ModuleLoadSequence
    {
        $storage = $this->fileStorageFactory->create(
            $this->getConfigurationFilePath($this->context->getDefaultShopId())
        );
        $chain = $this->node->normalize($storage->get());
        $loadSequence = $chain['moduleChains'][ModuleLoadSequence::KEY] ?? [];

        return new ModuleLoadSequence($loadSequence);
    }

    private function getConfigurationFilePath(int $shopId): string
    {
        return "{$this->context->getProjectConfigurationDirectory()}shops/{$shopId}.module_load_sequence.yaml";
    }
}
