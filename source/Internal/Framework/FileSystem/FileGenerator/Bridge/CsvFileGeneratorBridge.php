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
    /**
     * @var FileGeneratorInterface
     */
    private $fileGenerator;

    public function __construct(FileGeneratorInterface $fileGenerator)
    {
        $this->fileGenerator = $fileGenerator;
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
