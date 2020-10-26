<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Template;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class ModuleTemplatePathResolver implements ModuleTemplatePathResolverInterface
{
    /**
     * @var ActiveModulesDataProviderInterface
     */
    private $activeModulesDataProvider;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(ActiveModulesDataProviderInterface $activeModulesDataProvider, Filesystem $filesystem)
    {
        $this->activeModulesDataProvider = $activeModulesDataProvider;
        $this->filesystem = $filesystem;
    }

    public function resolve(string $templateKey): string
    {
        foreach ($this->activeModulesDataProvider->getTemplates() as $moduleId => $moduleTemplates) {
            foreach ($moduleTemplates as $template) {
                if ($template->getTemplateKey() === $templateKey) {
                    $fullTemplatePath = Path::join($this->getModuleAbsolutePath($moduleId), $template->getTemplatePath());
                    $this->validateTemplatePath($fullTemplatePath);

                    return $fullTemplatePath;
                }
            }
        }

        throw new ModuleTemplateKeyNotFound(
            "Module template with key $templateKey not found."
        );
    }

    private function getModuleAbsolutePath(string $moduleId): string
    {
        return $this->activeModulesDataProvider->getModulePaths()[$moduleId];
    }

    private function validateTemplatePath(string $fullTemplatePath): void
    {
        if (!$this->filesystem->exists($fullTemplatePath)) {
            throw new ModuleTemplateNotFound("Module template $fullTemplatePath not found.");
        }
    }
}
