<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Admin dynscreen list manager.
 * Arranges controll tabs and sets title.
 *
 * @subpackage dyn
 *
 * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
 */
class DynamicScreenList extends \OxidEsales\Eshop\Application\Controller\Admin\DynamicScreenController
{
    /**
     * Executes marent method parent::render() and returns mane of template
     * file "dynscreen_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData['menu'] = basename(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("menu"));

        return "dynscreen_list.tpl";
    }
}
