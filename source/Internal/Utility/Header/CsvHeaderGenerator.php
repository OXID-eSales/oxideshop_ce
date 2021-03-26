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
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");
        header("Content-Disposition: attachment");
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment;filename={$filename}");
    }
}
