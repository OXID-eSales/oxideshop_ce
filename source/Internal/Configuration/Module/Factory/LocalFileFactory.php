<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\Factory;

/**
 * Class LocalFileFactory
 *
 * @package OxidEsales\EshopCommunity\Internal\Configuration\Module\Factory
 */
class LocalFileFactory implements FileFactoryInterface
{
    /**
     * @var
     */
    private $filePath;
    /**
     * @var
     */
    private $mode;

    /**
     * LocalFileFactory constructor.
     *
     * @param string $filePath
     * @param string $mode
     */
    public function __construct(string $filePath, string $mode)
    {
        $this->filePath = $filePath;
        $this->mode = $mode;
    }

    /**
     * @return \SplFileObject
     */
    public function create(): \SplFileObject
    {
        return fopen($this->filePath, $this->mode);
    }
}
