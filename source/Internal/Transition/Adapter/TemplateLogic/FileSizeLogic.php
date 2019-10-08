<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class FileSizeLogic
{

    /**
     * @param int $size
     *
     * @return string
     */
    public function getFileSize($size): string
    {
        if ($size < 1024) {
            return $size . " B";
        }

        $size = $size / 1024;

        if ($size < 1024) {
            return sprintf("%.1f KB", $size);
        }

        $size = $size / 1024;

        if ($size < 1024) {
            return sprintf("%.1f MB", $size);
        }

        $size = $size / 1024;

        return sprintf("%.1f GB", $size);
    }
}
