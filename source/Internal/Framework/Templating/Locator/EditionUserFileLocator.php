<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class EditionUserFileLocator
 * @package OxidEsales\EshopCommunity\Internal\Framework\Templating\Locator
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
     *
     * @param AdminThemeBridgeInterface $adminThemeBridge
     * @param BasicContextInterface     $context
     * @param Filesystem                $fileSystem
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
        $editionPath = $this->context->getCommunityEditionSourcePath();
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
