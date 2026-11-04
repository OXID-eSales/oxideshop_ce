<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\AdminUiPathProviderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class AdminUiUserFileLocator implements NavigationFileLocatorInterface
{
    private string $fileName = 'user.xml';

    public function __construct(
        private readonly AdminUiPathProviderInterface $adminUiPathProvider,
        private readonly Filesystem $fileSystem
    ) {
    }

    public function locate(): array
    {
        $filePath = Path::join($this->adminUiPathProvider->getAbsolutePath(), $this->fileName);

        return $this->fileSystem->exists($filePath) ? [$filePath] : [];
    }
}
