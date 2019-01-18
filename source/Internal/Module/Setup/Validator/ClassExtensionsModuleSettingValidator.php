<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\InvalidClassExtensionNamespaceException;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\WrongModuleSettingException;

/**
 * @internal
 */
class ClassExtensionsModuleSettingValidator implements ModuleSettingValidatorInterface
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
     * @param ModuleSetting $moduleSetting
     * @param string        $moduleId
     * @param int           $shopId
     *
     * @throws WrongModuleSettingException
     */
    public function validate(ModuleSetting $moduleSetting, string $moduleId, int $shopId)
    {
        if (!$this->canValidate($moduleSetting)) {
            throw new WrongModuleSettingException($moduleSetting, self::class);
        }

        foreach ($moduleSetting->getValue() as $classToBePatched => $moduleClass) {
            if ($this->shopAdapter->isNamespace($classToBePatched)) {
                $this->validateClassToBePatchedNamespace($classToBePatched);
            }
        }
    }

    /**
     * @param ModuleSetting $moduleSetting
     * @return bool
     */
    public function canValidate(ModuleSetting $moduleSetting): bool
    {
        return $moduleSetting->getName() === ModuleSetting::CLASS_EXTENSIONS;
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
