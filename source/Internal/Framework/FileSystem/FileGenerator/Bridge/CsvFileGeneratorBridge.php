<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator\FileGeneratorInterface;

class CsvFileGeneratorBridge implements FileGeneratorBridgeInterface
{
    public function __construct(private FileGeneratorInterface $fileGenerator)
    {
    }

    /**
     * @param string $filename
     * @param array  $data
     */
    public function generate(string $filename, array $data): void
    {
        $this->fileGenerator->generate($filename, $data);
    }
}
