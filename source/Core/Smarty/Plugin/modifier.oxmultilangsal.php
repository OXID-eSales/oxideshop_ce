<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\Eshop\Core\Exception\StandardException;

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: Output translated salutation field
 * add [{$}] where you want to display content
 * -------------------------------------------------------------
 *
 * @param string $sIdent language constant ident
 *
 * @return string
 */
function smarty_modifier_oxmultilangsal($sIdent)
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
    } catch (StandardException $oEx) {
        // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
    }

    return $sTranslation;
}
