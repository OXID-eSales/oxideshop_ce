<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Header;

class CsvHeaderGenerator implements HeaderGeneratorInterface
{
    /**
     * @param string $filename
     */
    public function generate(string $filename): void
    {
        header("Pragma: no-cache");
        header("Expires: 0");
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment;filename={$filename}");
    }
}
