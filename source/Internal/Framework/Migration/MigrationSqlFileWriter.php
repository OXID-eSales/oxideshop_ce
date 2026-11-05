<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use LogicException;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlFileNotWritableException;
use RuntimeException;
use SplFileObject;

readonly class MigrationSqlFileWriter implements MigrationSqlFileWriterInterface
{
    public function clear(string $filePath): void
    {
        $this->open($filePath, 'w');
    }

    public function append(string $filePath, string $sql): void
    {
        $writtenBytes = $this->open($filePath, 'a')->fwrite($sql);

        if ($writtenBytes !== strlen($sql)) {
            throw new MigrationSqlFileNotWritableException($this->getErrorMessage($filePath));
        }
    }

    private function open(string $filePath, string $mode): SplFileObject
    {
        try {
            return new SplFileObject($filePath, $mode);
        } catch (RuntimeException | LogicException $exception) {
            throw new MigrationSqlFileNotWritableException($this->getErrorMessage($filePath), previous: $exception);
        }
    }

    private function getErrorMessage(string $filePath): string
    {
        return sprintf('Cannot write the migration SQL to "%s".', $filePath);
    }
}
