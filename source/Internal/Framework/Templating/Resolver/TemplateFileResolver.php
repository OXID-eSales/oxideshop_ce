<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidTemplateNameException;
use Webmozart\PathUtil\Path;

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

        return $this->appendDefaultFilenameExtension($templateName);
    }

    private function validateTemplateName(string $templateName): void
    {
        if (empty($templateName)) {
            throw new InvalidTemplateNameException('Template name can\'t be empty!');
        }
    }

    private function appendDefaultFilenameExtension(string $templateName): string
    {
        return !Path::getExtension($templateName)
            ? "$templateName.$this->filenameExtension"
            : $templateName;
    }
}
