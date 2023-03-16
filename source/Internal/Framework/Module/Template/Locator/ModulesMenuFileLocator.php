<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Template\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator\NavigationFileLocatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class ModulesMenuFileLocator implements NavigationFileLocatorInterface
{
    /** @var string */
    private $fileName = 'menu.xml';

    public function __construct(
        private ActiveModulesDataProviderInterface $activeModulesDataProvider,
        private Filesystem $filesystem
    ) {
    }

    /** @inheritDoc */
    public function locate(): array
    {
        $menuFiles = [];
        foreach ($this->activeModulesDataProvider->getModulePaths() as $modulePath) {
            $menuFilePath = Path::join($modulePath, $this->fileName);
            if ($this->filesystem->exists($menuFilePath)) {
                $menuFiles[] = $menuFilePath;
            }
        }
        return $menuFiles;
    }
}
