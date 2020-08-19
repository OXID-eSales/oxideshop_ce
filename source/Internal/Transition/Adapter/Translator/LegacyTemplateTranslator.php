<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Translator;

use OxidEsales\EshopCommunity\Core\Language;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Exception\TranslationNotFoundException;

class LegacyTemplateTranslator implements TranslatorInterface
{
    /**
     * @var Language
     */
    private $language;

    public function __construct()
    {
        $this->language = Registry::getLang();
    }

    /**
     * @param string $string
     * @return string
     * @throws TranslationNotFoundException
     */
    public function translate(string $string): string
    {
        $isAdmin = $this->language->isAdmin();
        $tplLang = $this->language->getTplLanguage();
        $translation = $this->language->translateString($string, $tplLang, $isAdmin);

        if (!$this->language->isTranslated()) {
            throw new TranslationNotFoundException();
        }

        return $translation;
    }

}