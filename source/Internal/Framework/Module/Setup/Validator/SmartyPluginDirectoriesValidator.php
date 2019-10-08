<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotExistentException;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotReadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration\SmartyPluginDirectoriesDataMapper;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSettingNotValidException;

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
        if ($configuration->hasSmartyPluginDirectories()) {
            $directories = [];

            foreach ($configuration->getSmartyPluginDirectories() as $directory) {
                $directories[] = $directory->getDirectory();
            }

            if ($this->isEmptyArray($directories)) {
                throw new ModuleSettingNotValidException(
                    'Module setting ' .
                    SmartyPluginDirectoriesDataMapper::MAPPING_KEY .
                    ' must be of type array but ' .
                    gettype($directories[0]) .
                    ' given'
                );
            }

            $fullPathToModule = $this->modulePathResolver->getFullModulePathFromConfiguration(
                $configuration->getId(),
                $shopId
            );

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

    /**
     * @param array $directories
     *
     * @return bool
     */
    private function isEmptyArray(array $directories): bool
    {
        if (count($directories) == 1) {
            if ($directories[0] === "") {
                return true;
            }
        }

        return false;
    }
}
