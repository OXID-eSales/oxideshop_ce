<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\InvalidClassExtensionNamespaceException;

/**
 * @internal
 */
class ClassExtensionsValidator implements ModuleConfigurationValidatorInterface
{
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * ClassExtensionsModuleSettingValidator constructor.
     * @param ShopAdapterInterface $shopAdapter
     */
    public function __construct(ShopAdapterInterface $shopAdapter)
    {
        $this->shopAdapter = $shopAdapter;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     *
     * @throws InvalidClassExtensionNamespaceException
     */
    public function validate(ModuleConfiguration $configuration, int $shopId)
    {
        if ($configuration->hasSetting(ModuleSetting::CLASS_EXTENSIONS)) {
            $moduleSetting = $configuration->getSetting(ModuleSetting::CLASS_EXTENSIONS);

            foreach ($moduleSetting->getValue() as $classToBePatched => $moduleClass) {
                if ($this->shopAdapter->isNamespace($classToBePatched)) {
                    $this->validateClassToBePatchedNamespace($classToBePatched);
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
