<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator;

/**
 * Class CsvFileGenerator
 */
class CsvFileGenerator implements FileGeneratorInterface
{
    /**
     * @param string $filename
     * @param array  $data
     */
    public function generate(string $filename, array $data): void
    {
        $file = fopen($filename, 'wb');

        foreach ($data as $value) {
            fputcsv($file, $value);
        }
        fclose($file);
    }
}
