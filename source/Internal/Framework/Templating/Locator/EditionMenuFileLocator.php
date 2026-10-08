<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class EditionMenuFileLocator implements NavigationFileLocatorInterface
{
    private string $fileName = 'menu.xml';

    public function __construct(
        private readonly string $themeName,
        private readonly BasicContextInterface $context,
        private readonly Filesystem $fileSystem
    ) {
    }

    public function locate(): array
    {
        $path = $this->context->getEdition() === Edition::Community
            ? $this->context->getSourcePath()
            : $this->context->getEditionSourcePath($this->context->getEdition());

        $filePath = Path::join(
            $path,
            'Application',
            'views',
            $this->themeName,
            $this->fileName,
        );

        return $this->fileSystem->exists($filePath) ? [$filePath] : [];
    }
}
