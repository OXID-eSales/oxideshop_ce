<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class EditionMenuFileLocator
 * @package OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator
 */
class EditionMenuFileLocator implements NavigationFileLocatorInterface
{
    /**
     * @var string
     */
    private $themeName;

    /**
     * @var string
     */
    private $fileName = 'menu.xml';

    /**
     * EditionMenuFileLocator constructor.
     */
    public function __construct(
        AdminThemeBridgeInterface $adminThemeBridge,
        private BasicContextInterface $context,
        private Filesystem $fileSystem
    ) {
        $this->themeName = $adminThemeBridge->getActiveTheme();
    }

    /**
     * Returns a full path for a given file name.
     *
     * @return array An array of file paths
     *
     * @throws \Exception
     */
    public function locate(): array
    {
        $filePath = $this->getMenuFileDirectory() . DIRECTORY_SEPARATOR . $this->fileName;
        return $this->validateFile($filePath);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function getMenuFileDirectory(): string
    {
        return $this->getEditionsRootPaths() . DIRECTORY_SEPARATOR .
            'Application' . DIRECTORY_SEPARATOR .
            'views' . DIRECTORY_SEPARATOR .
            $this->themeName;
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    private function getEditionsRootPaths(): string
    {
        $editionPath = $this->context->getSourcePath();
        if ($this->context->getEdition() === BasicContext::PROFESSIONAL_EDITION) {
            $editionPath = $this->context->getProfessionalEditionRootPath();
        } elseif ($this->context->getEdition() === BasicContext::ENTERPRISE_EDITION) {
            $editionPath = $this->context->getEnterpriseEditionRootPath();
        }

        return $editionPath;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    private function validateFile(string $file): array
    {
        $existingFiles = [];
        if ($this->fileSystem->exists($file)) {
            $existingFiles[] = $file;
        }
        return $existingFiles;
    }
}
