<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Multilanguage;

use OxidEsales\Eshop\Application\Controller\Admin\LanguageMain;

class LanguageMainControllerStub extends LanguageMain
{
    public function setLanguageData($languageData): void
    {
        $this->_aLangData = $languageData;
    }

    public function getLanguages()
    {
        return parent::getLanguages();
    }

    public function getAvailableLangBaseId()
    {
        return count($this->_aLangData['params']) - 1;
    }

    public function checkMultilangFieldsExistsInDb($sOxId)
    {
        return parent::checkMultilangFieldsExistsInDb($sOxId);
    }

    public function addNewMultilangFieldsToDb()
    {
        parent::addNewMultilangFieldsToDb();
    }
}
