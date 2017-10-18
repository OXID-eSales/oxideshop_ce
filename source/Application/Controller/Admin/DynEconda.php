<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin Econda manager.
 *
 * @subpackage dyn
 *
 * @deprecated v5.3 (2016-05-10); Econda will be moved to own module.
 */
class DynEconda extends \OxidEsales\Eshop\Application\Controller\Admin\ShopConfiguration
{
    /**
     * Creates shop object, passes shop data to Smarty engine and returns name of
     * template file "dyn_trusted.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData['oxid'] = $this->getConfig()->getShopId();

        return "dyn_econda.tpl";
    }

    /**
     * Returns view id
     *
     * @return string
     */
    public function getViewId()
    {
        return 'dyn_interface';
    }
}
