<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

use Symfony\Component\String\UnicodeString;

interface ModuleSettingServiceInterface
{
    public function getInteger(string $name, string $moduleId): int;
    public function getFloat(string $name, string $moduleId): float;
    public function getString(string $name, string $moduleId): UnicodeString;
    public function getBoolean(string $name, string $moduleId): bool;
    public function getCollection(string $name, string $moduleId): array;

    public function saveInteger(string $name, int $value, string $moduleId): void;
    public function saveFloat(string $name, float $value, string $moduleId): void;
    public function saveString(string $name, string $value, string $moduleId): void;
    public function saveBoolean(string $name, bool $value, string $moduleId): void;
    public function saveCollection(string $name, array $value, string $moduleId): void;
}
