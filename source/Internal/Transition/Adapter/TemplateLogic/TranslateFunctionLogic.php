<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class TranslateFunctionLogic
{
    /**
     * @param array $params
     *
     * @return string
     */
    public function getTranslation(array $params): string
    {
        startProfile("smarty_function_oxmultilang");
        $language = \OxidEsales\Eshop\Core\Registry::getLang();
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $activeShop = $config->getActiveShop();
        $isAdmin = $language->isAdmin();
        $ident = isset($params['ident']) ? $params['ident'] : 'IDENT MISSING';
        $args = isset($params['args']) ? $params['args'] : false;
        $suffix = isset($params['suffix']) ? $params['suffix'] : 'NO_SUFFIX';
        $showError = isset($params['noerror']) ? !$params['noerror'] : true;
        $tplLang = $language->getTplLanguage();
        if (!$isAdmin && $activeShop->isProductiveMode()) {
            $showError = false;
        }
        try {
            $translation = $language->translateString($ident, $tplLang, $isAdmin);
            $translationNotFound = !$language->isTranslated();
            if ('NO_SUFFIX' != $suffix) {
                $suffixTranslation = $language->translateString($suffix, $tplLang, $isAdmin);
            }
        } catch (\OxidEsales\Eshop\Core\Exception\LanguageException $oEx) {
            // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
        }
        if ($translationNotFound && isset($params['alternative'])) {
            $translation = $params['alternative'];
            $translationNotFound = false;
        }
        if (!$translationNotFound) {
            if ($args !== false) {
                if (is_array($args)) {
                    $translation = vsprintf($translation, $args);
                } else {
                    $translation = sprintf($translation, $args);
                }
            }
            if ('NO_SUFFIX' != $suffix) {
                $translation .= $suffixTranslation;
            }
        } elseif ($showError) {
            $translation = 'ERROR: Translation for ' . $ident . ' not found!';
        }
        stopProfile("smarty_function_oxmultilang");

        return $translation;
    }
}
