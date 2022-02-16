<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating\Resolver;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\Exception\InvalidTemplateNameException;

<<<<<<< HEAD
use function str_ends_with;

final class TemplateFileResolver implements TemplateFileResolverInterface
{
    private string $filenameExtension;

    public function __construct(
        string $filenameExtension
    ) {
=======
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
>>>>>>> OXDEV-4092 Refactor TemplateNameResolver
        $this->filenameExtension = $filenameExtension;
    }

    /** @inheritdoc */
    public function getFilename(string $templateName): string
    {
        $this->validateTemplateName($templateName);
<<<<<<< HEAD
        return $this->addExtension($templateName);
=======
        foreach ($this->supportedTemplateNameSuffixes as $suffix) {
            $templateName = preg_replace("/\.$suffix$/", '', $templateName);
        }
        return "$templateName.$this->filenameExtension";
>>>>>>> OXDEV-4092 Refactor TemplateNameResolver
    }

    private function validateTemplateName(string $templateName): void
    {
        if (empty($templateName)) {
            throw new InvalidTemplateNameException('Template name can\'t be empty!');
        }
    }
<<<<<<< HEAD

    private function addExtension(string $templateName): string
    {
        return str_ends_with($templateName, $this->filenameExtension)
            ? $templateName
            : "$templateName.$this->filenameExtension";
    }
=======
>>>>>>> OXDEV-4092 Refactor TemplateNameResolver
}
