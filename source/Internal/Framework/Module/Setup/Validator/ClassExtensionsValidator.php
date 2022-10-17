<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\InvalidClassExtensionNamespaceException;

class ClassExtensionsValidator implements ModuleConfigurationValidatorInterface
{
    public function __construct(private ShopAdapterInterface $shopAdapter)
    {
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     *
     * @throws InvalidClassExtensionNamespaceException
     */
    public function validate(ModuleConfiguration $configuration, int $shopId): void
    {
        if ($configuration->hasClassExtensions()) {
            foreach ($configuration->getClassExtensions() as $extension) {
                if ($this->shopAdapter->isNamespace($extension->getShopClassName())) {
                    $this->validateClassToBePatchedNamespace($extension->getShopClassName());
                }
            }
        }
    }

    /**
     * @param string $namespace
     * @throws InvalidClassExtensionNamespaceException
     */
    private function validateClassToBePatchedNamespace(string $namespace)
    {
        if ($this->shopAdapter->isShopEditionNamespace($namespace)) {
            throw new InvalidClassExtensionNamespaceException(
                'Module should not extend shop edition class: ' . $namespace
            );
        }

        if ($this->shopAdapter->isShopUnifiedNamespace($namespace) && !class_exists($namespace)) {
            throw new InvalidClassExtensionNamespaceException(
                'Module tries to extend non existent shop class: ' . $namespace
            );
        }
    }
}
