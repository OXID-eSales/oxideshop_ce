<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Webmozart\PathUtil\Path;

class ClassExtensionsChainDao implements ClassExtensionsChainDaoInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private FileStorageFactoryInterface $fileStorageFactory,
    )
    {
    }

    public function getChain(int $shopId): ClassExtensionsChain
    {
        return new ClassExtensionsChain(
            $this->storageExists($shopId) ? $this->getStorage($shopId)->get() : []
        );
    }

    public function saveChain(int $shopId, ClassExtensionsChain $chain): void
    {
        $this->getStorage($shopId)->save($chain->getChain());
    }

    private function storageExists(int $shopId): bool
    {
        return file_exists($this->getStorageFilePath($shopId));
    }

    private function getStorage(int $shopId): ArrayStorageInterface
    {
        return $this->fileStorageFactory->create($this->getStorageFilePath($shopId));
    }

    private function getStorageFilePath(int $shopId): string
    {
        return Path::join($this->context->getShopConfigurationDirectory($shopId), 'class_extension_chain.yaml');
    }
}