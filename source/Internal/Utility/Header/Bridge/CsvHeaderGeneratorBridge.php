<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Header\Bridge;

use OxidEsales\EshopCommunity\Internal\Utility\Header\HeaderGeneratorInterface;

class CsvHeaderGeneratorBridge implements HeaderGeneratorBridgeInterface
{
    /**
     * @var HeaderGeneratorInterface
     */
    private $headerGenerator;

    public function __construct(HeaderGeneratorInterface $headerGenerator)
    {
        $this->headerGenerator = $headerGenerator;
    }

    /**
     * @param string $filename
     */
    public function generate(string $filename): void
    {
        $this->headerGenerator->generate($filename);
    }
}
