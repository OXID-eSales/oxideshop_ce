<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;

/**
 * Smarty renderer class
 * Renders smarty template with given parameters and returns rendered body.
 *
 */
class SmartyRenderer
{
    /**
     * Template renderer
     *
     * @param string $sTemplateName Template name.
     * @param array  $aViewData     Array of view data (optional).
     *
     * @return string
     */
    public function renderTemplate($sTemplateName, $aViewData = [])
    {
        $oSmarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();

        foreach ($aViewData as $key => $value) {
            $oSmarty->assign($key, $value);
        }

        $sBody = $oSmarty->fetch($sTemplateName);

        return $sBody;
    }
}
