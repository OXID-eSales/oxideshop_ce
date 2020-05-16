<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: Modifies provided language constant with it's translation
 * usage: [{$val|oxmultilangassign}]
 * -------------------------------------------------------------
 *
 * @param string $sIdent language constant ident
 * @param mixed  $args   for constants using %s notations
 *
 * @return string
 */
function smarty_modifier_oxmultilangassign($sIdent, $args = null)
{
    if (!isset($sIdent)) {
        $sIdent = 'IDENT MISSING';
    }

    $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
    $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
    $oShop = $oConfig->getActiveShop();
    $iLang = $oLang->getTplLanguage();
    $blShowError = true;

    if ($oShop->isProductiveMode()) {
        $blShowError = false;
    }

    try {
        $sTranslation = $oLang->translateString($sIdent, $iLang, $oLang->isAdmin());
        $blTranslationNotFound = !$oLang->isTranslated();
    } catch (\OxidEsales\Eshop\Core\Exception\LanguageException $oEx) {
        // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
    }

    if (!$blTranslationNotFound) {
        if ($args) {
            if (is_array($args)) {
                $sTranslation = vsprintf($sTranslation, $args);
            } else {
                $sTranslation = sprintf($sTranslation, $args);
            }
        }
    } elseif ($blShowError) {
        $sTranslation = 'ERROR: Translation for ' . $sIdent . ' not found!';
    }

    return $sTranslation;
}
