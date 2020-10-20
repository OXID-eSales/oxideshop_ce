<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\{
    Locator\ModuleTranslationFileLocatorAbstract as LocatorAbstract,
    Locator\FrontendModuleTranslationFileLocatorInterface as LocatorInterface};
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class FrontendModuleTranslationFileLocator extends LocatorAbstract implements LocatorInterface
{
    /** @var ActiveModulesDataProviderInterface */
    private $activeModulesDataProvider;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(
        ActiveModulesDataProviderInterface $activeModulesDataProvider,
        Filesystem $filesystem
    ) {
        $this->activeModulesDataProvider = $activeModulesDataProvider;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $lang
     *
     * @return array
     */
    public function locate(string $lang): array
    {
        $langFiles = [];

        foreach ($this->activeModulesDataProvider->getModulePaths() as $moduleLangPath) {
            $moduleLangPath = Path::join(
                $this->checkAndAddApplicationFolder($this->filesystem, $moduleLangPath),
                'translations',
                $lang
            );

            $langFiles = $this->appendLangFiles($langFiles, $moduleLangPath);
        }

        return $langFiles;
    }
}
