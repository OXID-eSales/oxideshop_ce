<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Language;

class DefaultLanguage
{
    /**
     * @var string
     */
    private $language;

    /**
     * @var string[]
     */
    private $availableLanguages = ['en', 'de'];

    /**
     * @throws IncorrectLanguageException
     */
    public function __construct(string $language)
    {
        if (!\in_array($language, $this->availableLanguages, true)) {
            throw new IncorrectLanguageException('Invalid language argument: ' . $language . ', available languages: ' . implode(', ', $this->availableLanguages));
        }

        $this->language = $language;
    }

    public function getCode(): string
    {
        return $this->language;
    }
}
