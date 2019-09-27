<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\NamespaceInformationProvider;

/**
 * Module metadata validation class.
 * Used for validating if module metadata exists and is usable.
 *
 * @deprecated since v6.4.0 (2019-05-24); Validation was moved to Internal\Framework\Module package and will be executed during the module activation.
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleMetadataValidator implements \OxidEsales\Eshop\Core\Contract\IModuleValidator
{
    /**
     * Validates module metadata.
     * Return true if module metadata is valid.
     * Return false if module metadata is not valid, or if metadata file does not exist.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $module object to validate metadata.
     *
     * @return bool
     */
    public function validate(\OxidEsales\Eshop\Core\Module\Module $module)
    {
        return file_exists($module->getMetadataPath());
    }

    /**
     * Check module metadata for incorrect namespace shop classes.
     * Class might be misspelled or not found in Unified Namespace.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $module
     *
     * @throws \OxidEsales\Eshop\Core\Exception\ModuleValidationException
     */
    public function checkModuleExtensionsForIncorrectNamespaceClasses(\OxidEsales\Eshop\Core\Module\Module $module)
    {
        $incorrect = $this->getIncorrectExtensions($module);
        if (!empty($incorrect)) {
            $message = $this->prepareMessage('MODULE_METADATA_PROBLEMATIC_DATA_IN_EXTEND', $incorrect);
            throw new \OxidEsales\Eshop\Core\Exception\ModuleValidationException($message);
        }
    }

    /**
     * Getter for possible incorrect extension info in metadata.php.
     * If the module patches a namespace class it must either belong to the shop
     * Unified Namespace or to another module.
     *
     * @param \OxidEsales\Eshop\Core\Module\Module $module
     *
     * @return array
     */
    public function getIncorrectExtensions(\OxidEsales\Eshop\Core\Module\Module $module)
    {
        $incorrect = [];
        $rawExtensions = $module->getExtensions();

        foreach ($rawExtensions as $classToBePatched => $moduleClass) {
            if (NamespaceInformationProvider::isNamespacedClass($classToBePatched)
                 && (
                     NamespaceInformationProvider::classBelongsToShopEditionNamespace($classToBePatched)
                      || (NamespaceInformationProvider::classBelongsToShopUnifiedNamespace($classToBePatched) && !class_exists($classToBePatched))
                    )
                ) {
                $incorrect[$classToBePatched] = $moduleClass;
            }
        }
        return $incorrect;
    }

    /**
     * @param string $languageConstant
     * @param array  $incorrect
     *
     * @return string
     */
    protected function prepareMessage($languageConstant, $incorrect = [])
    {
        $additionalInformation = '';
        foreach ($incorrect as $patchee => $patch) {
            $additionalInformation .= $patchee . ' => ' . $patch . ', ';
        }
        $additionalInformation = rtrim($additionalInformation, ', ');
        $message = sprintf(Registry::getLang()->translateString($languageConstant, null, true), $additionalInformation);

        return $message;
    }
}
