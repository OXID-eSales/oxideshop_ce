<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\ShopIdCalculator;

/**
 * Admin shop manager.
 * Returns template, that arranges two other templates ("shop_list"
 * and "shop_main") to frame.
 * Admin Menu: Main Menu -> Core Settings.
 */
class ShopController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    const CURRENT_TEMPLATE = 'shop';

    /**
     * Executes parent method parent::render() and returns name of template
     * file "shop".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData['currentadminshop'] = ShopIdCalculator::BASE_SHOP_ID;

        return static::CURRENT_TEMPLATE;
    }
}
