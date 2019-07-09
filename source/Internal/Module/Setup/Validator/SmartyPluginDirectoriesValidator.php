<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotExistentException;
use OxidEsales\EshopCommunity\Internal\Common\Exception\DirectoryNotReadableException;

/**
 * Class SmartyPluginDirectoriesModuleSettingValidator
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 *
 */
class SmartyPluginDirectoriesValidator implements ModuleConfigurationValidatorInterface
{
    /**
     * @var ModulePathResolverInterface
     */
    private $modulePathResolver;

    /**
     * @param ModulePathResolverInterface $modulePathResolver
     */
    public function __construct(ModulePathResolverInterface $modulePathResolver)
    {
        $this->modulePathResolver = $modulePathResolver;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @param int                 $shopId
     *
     * @throws DirectoryNotExistentException
     * @throws DirectoryNotReadableException
     * @throws ModuleSettingNotValidException
     */
    public function validate(ModuleConfiguration $configuration, int $shopId)
    {
        if ($configuration->hasSetting(ModuleSetting::SMARTY_PLUGIN_DIRECTORIES)) {
            $directories = $configuration->getSetting(ModuleSetting::SMARTY_PLUGIN_DIRECTORIES)->getValue();

            if (!is_array($directories)) {
                throw new ModuleSettingNotValidException(
                    'Module setting ' .
                    ModuleSetting::SMARTY_PLUGIN_DIRECTORIES .
                    ' must be of type array but ' .
                    gettype($directories) .
                    ' given'
                );
            }

            $fullPathToModule = $this->modulePathResolver->getFullModulePathFromConfiguration($configuration->getId(), $shopId);

            foreach ($directories as $directory) {
                $fullPathSmartyPluginDirectory = $fullPathToModule . DIRECTORY_SEPARATOR . $directory;
                if (!is_dir($fullPathSmartyPluginDirectory)) {
                    throw new DirectoryNotExistentException(
                        'Directory ' . $fullPathSmartyPluginDirectory . ' does not exist.'
                    );
                }
                if (!is_readable($fullPathSmartyPluginDirectory)) {
                    throw new DirectoryNotReadableException(
                        'Directory ' . $fullPathSmartyPluginDirectory . ' not readable.'
                    );
                }
            }
        }
    }
}
