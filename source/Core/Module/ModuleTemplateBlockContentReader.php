<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

/**
 * Provides a way to get content from module template block file.
 *
 * @deprecated 6.6.0
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleTemplateBlockContentReader
{
    /**
     * Read and return content for template block file.
     * Path to template block file is provided via $pathFormatter.
     * Throw exception if file does not exist or is not readable.
     *
     * @param \OxidEsales\Eshop\Core\Module\ModuleTemplateBlockPathFormatter $pathFormatter
     *
     * @throws \oxException
     *
     * @return string
     */
    public function getContent($pathFormatter)
    {
        if (!$pathFormatter instanceof \OxidEsales\Eshop\Core\Module\ModuleTemplateBlockPathFormatter) {
            $exceptionMessage = 'Provided object is not an instance of class %s or does not have method getPath().';
            throw oxNew('oxException', sprintf($exceptionMessage, \OxidEsales\Eshop\Core\Module\ModuleTemplateBlockPathFormatter::class));
        }

        $filePath = $pathFormatter->getPath();

        if (!file_exists($filePath)) {
            $exceptionMessage = "Template block file (%s) was not found for module '%s'.";
            throw oxNew('oxException', sprintf($exceptionMessage, $filePath, $pathFormatter->getModuleId()));
        }

        if (!is_readable($filePath)) {
            $exceptionMessage = "Template block file (%s) is not readable for module '%s'.";
            throw oxNew('oxException', sprintf($exceptionMessage, $filePath, $pathFormatter->getModuleId()));
        }

        return file_get_contents($filePath);
    }
}
