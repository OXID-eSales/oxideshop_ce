<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Smarty;

interface SmartyContextInterface
{
    public function getTemplateEngineDebugMode(): bool;

    public function showTemplateNames(): bool;

    public function getTemplateSecurityMode(): bool;

    public function getTemplateCompileDirectory(): string;

    public function getTemplateDirectories(): array;

    public function getTemplateCompileId(): string;

    public function getTemplateCompileCheckMode(): bool;

    public function getSmartyPluginDirectories(): array;

    public function getTemplatePhpHandlingMode(): int;

    /**
     * @param string $templateName
     */
    public function getTemplatePath($templateName): string;

    public function getSourcePath(): string;
}
