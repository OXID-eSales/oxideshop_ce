<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidTemplateNameException;

use function preg_replace;

final class TemplateFileResolver implements TemplateFileResolverInterface
{
    private array $supportedTemplateNameSuffixes;
    private string $filenameExtension;

    public function __construct(
        array $supportedTemplateNameSuffixes,
        string $filenameExtension
    ) {
        $this->supportedTemplateNameSuffixes = $supportedTemplateNameSuffixes;
        $this->filenameExtension = $filenameExtension;
    }

    /** @inheritdoc */
    public function getFilename(string $templateName): string
    {
        $this->validateTemplateName($templateName);
        foreach ($this->supportedTemplateNameSuffixes as $suffix) {
            $templateName = preg_replace("/\.$suffix$/", '', $templateName);
        }
        return "$templateName.$this->filenameExtension";
    }

    private function validateTemplateName(string $templateName): void
    {
        if (empty($templateName)) {
            throw new InvalidTemplateNameException('Template name can\'t be empty!');
        }
    }
}
