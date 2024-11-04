<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class EditionUserFileLocator implements NavigationFileLocatorInterface
{
    private string $themeName;
    private string $fileName = 'user.xml';

    public function __construct(
        AdminThemeBridgeInterface $adminThemeBridge,
        private readonly BasicContextInterface $context,
        private readonly Filesystem $fileSystem
    ) {
        $this->themeName = $adminThemeBridge->getActiveTheme();
    }

    public function locate(): array
    {
        $filePath = Path::join(
            $this->context->getEditionSourcePath($this->context->getEdition()),
            'Application',
            'views',
            $this->themeName,
            $this->fileName,
        );

        return $this->fileSystem->exists($filePath) ? [$filePath] : [];
    }
}
