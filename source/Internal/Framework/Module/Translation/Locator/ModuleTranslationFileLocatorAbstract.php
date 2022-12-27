<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Translation\Locator;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

abstract class ModuleTranslationFileLocatorAbstract
{
    protected function checkAndAddApplicationFolder(Filesystem $filesystem, string $moduleLangPath): string
    {
        $applicationFolder = Path::join($moduleLangPath, 'Application');

        if ($filesystem->exists($applicationFolder)) {
            return $applicationFolder;
        }

        return $moduleLangPath;
    }

    protected function appendLangFiles(array $langFiles, string $moduleLangPath): array
    {
        $files = glob(Path::join($moduleLangPath, '*_lang.php'));

        if (\is_array($files) && count($files)) {
            foreach ($files as $file) {
                if (!strpos($file, 'cust_lang.php')) {
                    $langFiles[] = $file;
                }
            }
        }

        return $langFiles;
    }
}
