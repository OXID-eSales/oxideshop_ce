<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Language;

use function in_array;

class DefaultLanguage
{
    private string $language;

    private array $availableLanguages = ['en', 'de'];

    /**
     * @throws IncorrectLanguageException
     */
    public function __construct(string $language)
    {
        if (in_array($language, $this->availableLanguages, true)) {
            $this->language = $language;
        } else {
            throw new IncorrectLanguageException(
                sprintf(
                    'Invalid language argument: %s, available languages: %s',
                    $language,
                    implode(', ', $this->availableLanguages)
                )
            );
        }
    }

    public function getCode(): string
    {
        return $this->language;
    }
}
