<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

class LanguageMainHelper extends Language_Main
{
    public function getLanguageData()
    {
        return $this->_aLangData;
    }

    public function setLanguageData($languageData)
    {
        $this->_aLangData = $languageData;
    }
}
