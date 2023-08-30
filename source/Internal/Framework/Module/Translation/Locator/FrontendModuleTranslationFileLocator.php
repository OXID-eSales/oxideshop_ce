<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Locator;

// phpcs:disable
use OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Locator\ModuleTranslationFileLocatorAbstract as LocatorAbstract;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Locator\FrontendModuleTranslationFileLocatorInterface as LocatorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
// phpcs:enable

class FrontendModuleTranslationFileLocator extends LocatorAbstract implements LocatorInterface
{
    public function __construct(
        private ActiveModulesDataProviderInterface $activeModulesDataProvider,
        private Filesystem $filesystem
    ) {
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
