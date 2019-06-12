<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Smarty function
 * -------------------------------------------------------------
 * Purpose: Output help popup icon and help text
 * add [{oxinputhelp ident="..."}] where you want to display content
 * -------------------------------------------------------------
 *
 * @param array  $params  params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @return string
 */
function smarty_function_oxinputhelp($params, &$smarty)
{
    $sIdent = $params['ident'];
    $oLang = \OxidEsales\Eshop\Core\Registry::getLang();
    $iLang  = $oLang->getTplLanguage();

    try {
        $sTranslation = $oLang->translateString($sIdent, $iLang, $blAdmin);
    } catch (\OxidEsales\Eshop\Core\Exception\LanguageException $oEx) {
        // is thrown in debug mode and has to be caught here, as smarty hangs otherwise!
    }

    if (!$sTranslation || $sTranslation == $sIdent) {
        //no translation, return empty string
        return '';
    }

    //name of template file where is stored message text
    $sTemplate = 'inputhelp.tpl';

    $smarty->assign('sHelpId', $sIdent);
    $smarty->assign('sHelpText', $sTranslation);

    return $smarty->fetch($sTemplate);
}
