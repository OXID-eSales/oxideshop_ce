<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

class HtaccessDao implements HtaccessDaoInterface
{
    private const DIRECTIVE_REWRITE_BASE = 'RewriteBase';
    /** @var string */
    private $contents;

    public function __construct(private string $filePath)
    {
    }

    /** @param string $rewriteBase */
    public function setRewriteBase(string $rewriteBase): void
    {
        $this->readFile();
        $this->updateDirective(self::DIRECTIVE_REWRITE_BASE, $rewriteBase);
        $this->writeFile();
    }

    private function readFile(): void
    {
        $this->checkFileAccess();
        $this->contents = file_get_contents($this->filePath);
    }

    /**
     * @param string $key
     * @param string $value
     */
    private function updateDirective(string $key, string $value): void
    {
        $this->contents = preg_replace("/$key.*/", "$key $value", $this->contents);
    }

    private function writeFile(): void
    {
        file_put_contents($this->filePath, $this->contents);
    }

    /** @throws HtaccessAccessException */
    private function checkFileAccess(): void
    {
        clearstatcache();
        if (!is_readable($this->filePath) || !is_writable($this->filePath)) {
            throw new HtaccessAccessException("File not found or not accessible: `{$this->filePath}`");
        }
    }
}
