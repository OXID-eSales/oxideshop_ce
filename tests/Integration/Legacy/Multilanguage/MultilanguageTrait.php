<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Multilanguage;

use OxidEsales\Eshop\Application\Controller\Admin\LanguageMain;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

use function chr;

trait MultilanguageTrait
{
    private LanguageMain $controller;

    protected function createLanguages(): int
    {
        /**
         * @var $extraLanguagesCount - number of languages to add to start creating additional _set* tables
         * @see \OxidEsales\EshopCommunity\Core\DbMetaDataHandler::ensureMultiLanguageFields
         */
        $extraLanguagesCount = 9;
        for ($i = 0; $i < $extraLanguagesCount; $i++) {
            $languageCode = str_repeat(chr(97 + $i), 2);
            $languageId = $this->insertLanguage($languageCode);
        }
        Registry::set(Language::class, null);
        Registry::set(TableViewNameGenerator::class, null);

        return $languageId;
    }

    /**
     * Use admin controller to mock "creation of a new language", the changes in the DB are verified later in tests.
     * This approach is very slow.
     */
    private function insertLanguage(string $languageCode): int
    {
        $baseId = $this->getController()->getAvailableLangBaseId();
        $languages = $this->getController()->getLanguages();
        $languages['params'][$languageCode] = [
            'baseId' => $baseId,
            'active' => 1,
            'sort' => $baseId * 100,
        ];
        $languages['lang'][$languageCode] = $languageCode;
        $this->getController()->setLanguageData($languages);

        Registry::getConfig()->saveShopConfVar('aarr', 'aLanguageParams', $languages['params']);
        Registry::getConfig()->saveShopConfVar('aarr', 'aLanguages', $languages['lang']);
        Registry::getConfig()->saveShopConfVar('arr', 'aLanguageURLs', $languages['urls']);
        Registry::getConfig()->saveShopConfVar('arr', 'aLanguageSSLURLs', $languages['sslUrls']);

        if (!$this->getController()->checkMultilangFieldsExistsInDb($languageCode)) {
            $this->getController()->addNewMultilangFieldsToDb();
        }

        return $baseId;
    }

    private function getController(): LanguageMain
    {
        if (!isset($this->controller)) {
            $this->controller = new LanguageMainControllerStub();
            $this->controller->render();
        }
        return $this->controller;
    }
}
