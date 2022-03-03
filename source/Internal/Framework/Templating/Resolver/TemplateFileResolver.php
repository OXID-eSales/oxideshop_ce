<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidTemplateNameException;

use function str_ends_with;

final class TemplateFileResolver implements TemplateFileResolverInterface
{
    private string $filenameExtension;

    public function __construct(
        string $filenameExtension
    ) {
        $this->filenameExtension = $filenameExtension;
    }

    /** @inheritdoc */
    public function getFilename(string $templateName): string
    {
        $this->validateTemplateName($templateName);
        return $this->addExtension($templateName);
    }

    private function validateTemplateName(string $templateName): void
    {
        if (empty($templateName)) {
            throw new InvalidTemplateNameException('Template name can\'t be empty!');
        }
    }

    private function addExtension(string $templateName): string
    {
        return str_ends_with($templateName, $this->filenameExtension)
            ? $templateName
            : "$templateName.$this->filenameExtension";
    }
}
