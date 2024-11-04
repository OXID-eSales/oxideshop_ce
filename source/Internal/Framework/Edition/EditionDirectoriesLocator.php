<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Edition;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotExistentException;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectDirectoriesLocator;
use Symfony\Component\Filesystem\Path;

readonly class EditionDirectoriesLocator
{
    public function getEditionRootPath(Edition $edition): string
    {
        $projectDirectoriesLocator = new ProjectDirectoriesLocator();
        $path = Path::join(
            $projectDirectoriesLocator->getVendorPath(),
            EditionPaths::from($edition->value)->getVendorFolderName(),
            EditionPaths::from($edition->value)->getProjectFolderName(),
        );
        if (!is_dir($path)) {
            if ($edition->isCommunityEdition()) {
                return $projectDirectoriesLocator->getRootPath();
            }
            throw new DirectoryNotExistentException("Root directory for {$edition->name} does not exist!");
        }
        return $path;
    }

    public function getEditionSourcePath(Edition $edition): string
    {
        return Path::join(
            $this->getEditionRootPath($edition),
            EditionPaths::from($edition->value)->getSourceFolderName(),
        );
    }
}
