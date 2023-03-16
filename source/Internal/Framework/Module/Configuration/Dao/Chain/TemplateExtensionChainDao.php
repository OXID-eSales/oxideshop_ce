<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\Chain;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleTemplateExtensionChain;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

class TemplateExtensionChainDao implements TemplateExtensionChainDaoInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private FileStorageFactoryInterface $fileStorageFactory,
    )
    {
    }

    public function getChain(int $shopId): ModuleTemplateExtensionChain
    {
        return new ModuleTemplateExtensionChain(
            $this->storageExists($shopId) ? $this->getStorage($shopId)->get() : []
        );
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
        return Path::join($this->context->getShopConfigurationDirectory($shopId), 'template_extension_chain.yaml');
    }

}
