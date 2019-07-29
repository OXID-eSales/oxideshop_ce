<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic;

/**
 * Class TranslateSalutationLogic
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class TranslateSalutationLogic
{

    /**
     * @param string $sIdent
     *
     * @return string
     */
    public function translateSalutation(string $sIdent = null): string
    {
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
        $iLang = $oLang->getTplLanguage();

        if (!isset($iLang)) {
            $iLang = $oLang->getBaseLanguage();
            if (!isset($iLang)) {
                $iLang = 0;
            }
        }

        try {
            $sTranslation = $oLang->translateString($sIdent, $iLang, $oLang->isAdmin());
        } catch (\OxidEsales\Eshop\Core\Exception\LanguageException $oEx) {
            // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
        }

        return $sTranslation ?: '';
    }
}
