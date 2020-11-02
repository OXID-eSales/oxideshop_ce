<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\{
    Locator\AdminAreaModuleTranslationFileLocatorInterface as LocatorInterface,
    Locator\ModuleTranslationFileLocatorAbstract as LocatorAbstract
};
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class AdminAreaModuleTranslationFileLocator extends LocatorAbstract implements LocatorInterface
{
    /**
     * @var ModulesDataProviderInterface
     */
    private $modulesDataProvider;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $adminThemeName;

    public function __construct(
        ModulesDataProviderInterface $modulesDataProvider,
        Filesystem $filesystem,
        string $adminThemeName
    ) {
        $this->modulesDataProvider = $modulesDataProvider;
        $this->filesystem = $filesystem;
        $this->adminThemeName = $adminThemeName;
    }

    public function locate(string $lang): array
    {
        $langFiles = [];

        foreach ($this->modulesDataProvider->getModulePaths() as $moduleLangPath) {
            $moduleLangPath = Path::join(
                $this->checkAndAddApplicationFolder($this->filesystem, $moduleLangPath),
                'views',
                $this->adminThemeName,
                $lang
            );

            $langFiles = $this->appendLangFiles($langFiles, $moduleLangPath);

            $langFiles = $this->appendModuleOptionsFile($langFiles, $moduleLangPath);
        }

        return $langFiles;
    }

    private function appendModuleOptionsFile(array $langFiles, string $moduleLangPath): array
    {
        $langFilePath = Path::join($moduleLangPath, 'module_options.php');

        if ($this->filesystem->exists($langFilePath)) {
            $langFiles[] = $langFilePath;
        }

        return $langFiles;
    }
}
