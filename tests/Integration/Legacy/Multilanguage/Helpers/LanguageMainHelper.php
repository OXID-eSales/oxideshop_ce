<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

class LanguageMainHelper extends Language_Main
{
    public function getLanguageData()
    {
        return $this->_aLangData;
    }

    public function setLanguageData($languageData): void
    {
        $this->_aLangData = $languageData;
    }
}
