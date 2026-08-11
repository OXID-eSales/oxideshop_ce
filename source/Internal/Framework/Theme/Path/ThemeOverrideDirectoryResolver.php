<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Path;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

readonly class ThemeOverrideDirectoryResolver implements ThemeOverrideDirectoryResolverInterface
{
    private const TEMPLATES_DIRECTORY_NAME = 'tpl';

    public function __construct(
        private BasicContextInterface $context,
        private ActiveLocaleProviderInterface $activeLocaleProvider,
        private Filesystem $filesystem,
    ) {
    }

    public function resolve(string $themeId, int $shopId): array
    {
        $themeDirectory = Path::join($this->context->getSourcePath(), 'Application', 'views', $themeId);
        $languageCode = $this->activeLocaleProvider->getActiveLocale()->getCode();

        return array_values(array_filter(
            [
                Path::join($themeDirectory, (string) $shopId, $languageCode, self::TEMPLATES_DIRECTORY_NAME),
                Path::join($themeDirectory, (string) $shopId, self::TEMPLATES_DIRECTORY_NAME),
                Path::join($themeDirectory, $languageCode, self::TEMPLATES_DIRECTORY_NAME),
            ],
            fn(string $directory): bool => $this->filesystem->exists($directory)
        ));
    }
}
