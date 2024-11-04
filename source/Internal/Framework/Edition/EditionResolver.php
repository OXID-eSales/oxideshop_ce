<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Edition;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\DirectoryNotExistentException;

readonly class EditionResolver
{
    public function getEdition(): Edition
    {
        $editionDirectoriesLocator = new EditionDirectoriesLocator();
        try {
            $editionDirectoriesLocator->getEditionRootPath(Edition::Enterprise);
            return Edition::Enterprise;
        } catch (DirectoryNotExistentException) {
            try {
                $editionDirectoriesLocator->getEditionRootPath(Edition::Professional);
                return Edition::Professional;
            } catch (DirectoryNotExistentException) {
                return Edition::Community;
            }
        }
    }
}
