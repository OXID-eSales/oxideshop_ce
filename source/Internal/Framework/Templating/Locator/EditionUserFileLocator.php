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
 * Class EditionUserFileLocator.
 */
class EditionUserFileLocator implements NavigationFileLocatorInterface
{
    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @var string
     */
    private $themeName;

    /**
     * @var string
     */
    private $fileName = 'user.xml';

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * EditionUserFileLocator constructor.
     */
    public function __construct(
        AdminThemeBridgeInterface $adminThemeBridge,
        BasicContextInterface $context,
        Filesystem $fileSystem
    ) {
        $this->themeName = $adminThemeBridge->getActiveTheme();
        $this->context = $context;
        $this->fileSystem = $fileSystem;
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
        $filePath = $this->getMenuFileDirectory() . \DIRECTORY_SEPARATOR . $this->fileName;

        return $this->validateFile($filePath);
    }

    /**
     * @throws \Exception
     */
    private function getMenuFileDirectory(): string
    {
        return $this->getEditionsRootPaths() . \DIRECTORY_SEPARATOR .
            'Application' . \DIRECTORY_SEPARATOR .
            'views' . \DIRECTORY_SEPARATOR .
            $this->themeName;
    }

    /**
     * @throws \Exception
     */
    private function getEditionsRootPaths(): string
    {
        $editionPath = $this->context->getCommunityEditionSourcePath();
        if (BasicContext::PROFESSIONAL_EDITION === $this->context->getEdition()) {
            $editionPath = $this->context->getProfessionalEditionRootPath();
        } elseif (BasicContext::ENTERPRISE_EDITION === $this->context->getEdition()) {
            $editionPath = $this->context->getEnterpriseEditionRootPath();
        }

        return $editionPath;
    }

    private function validateFile(string $file): array
    {
        $existingFiles = [];
        if ($this->fileSystem->exists($file)) {
            $existingFiles[] = $file;
        }

        return $existingFiles;
    }
}
