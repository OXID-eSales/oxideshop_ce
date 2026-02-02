<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Templating;

use OxidEsales\Twig\Resolver\DataObject\NamespacedDirectory;
use OxidEsales\Twig\Resolver\TemplateDirectoryResolverInterface;

/**
 * @internal
 */
readonly class BundleTemplateDirectoryResolver implements TemplateDirectoryResolverInterface
{
    /**
     * @param array<string, array{path: string, namespace: string}> $bundlesMetadata
     */
    public function __construct(private array $bundlesMetadata)
    {
    }

    /**
     * @return NamespacedDirectory[]
     */
    public function getTemplateDirectories(): array
    {
        $directories = [];

        foreach ($this->bundlesMetadata as $name => $metadata) {
            foreach ($this->candidateDirectories($metadata['path']) as $dir) {
                if (is_dir($dir)) {
                    $directories[] = new NamespacedDirectory($name, $dir);
                    break;
                }
            }
        }

        return $directories;
    }

    /**
     * @return string[]
     */
    private function candidateDirectories(string $bundlePath): array
    {
        return [
            $bundlePath . '/Resources/templates',
            $bundlePath . '/Resources/views',
            $bundlePath . '/templates',
        ];
    }
}
