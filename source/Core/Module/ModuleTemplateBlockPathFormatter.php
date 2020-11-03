<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\FileSystem\FileSystem;
use OxidEsales\Eshop\Core\Registry;

/**
 * Forms path to module block template.
 *
 * @deprecated 6.6.0
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleTemplateBlockPathFormatter
{
    /** @var string Module id */
    private $moduleId;

    /** @var string Path to module file name which defines content to place in Shop block */
    private $fileName;

    /** @var string Path to modules directory inside the shop. */
    private $modulesPath;

    /**
     * @param string $path
     */
    public function setModulesPath($path)
    {
        $this->modulesPath = $path;
    }

    /**
     * @param string $moduleId
     */
    public function setModuleId($moduleId)
    {
        $this->moduleId = $moduleId;
    }

    /**
     * @return string
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }

    /**
     * @param string $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Return full path to module file which defines content to place in Shop block.
     *
     * @throws \oxException
     *
     * @return string
     */
    public function getPath()
    {
        if (is_null($this->moduleId) || is_null($this->fileName)) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class);
        }

        $fileName = $this->fileName;

        // for < 4.6 modules, since 4.7/5.0 insert in oxtplblocks the full file name and path
        if (basename($fileName) === $fileName) {
            // for 4.5 modules, since 4.6 insert in oxtplblocks the full file name
            if (substr($fileName, -4) !== '.tpl') {
                $fileName = $fileName . ".tpl";
            }

            $fileName = "out/blocks/$fileName";
        }

        $activeModuleInfo = (array) Registry::getConfig()->getConfigParam('aModulePaths');

        if (!array_key_exists($this->moduleId, $activeModuleInfo)) {
            throw oxNew('oxException', 'Module: ' . $this->moduleId . ' is not active.');
        }

        $modulePath = $activeModuleInfo[$this->moduleId];

        $fileSystem = oxNew(FileSystem::class);

        return $fileSystem->combinePaths($this->modulesPath, $modulePath, $fileName);
    }
}
